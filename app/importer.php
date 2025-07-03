<?php

class Importer
{
    const IMPORT_ID_FRENCH = 1;
    const IMPORT_ID_ENGLISH = 2;

    public function __construct()
    {
        add_filter('http_request_args', [$this, 'modifyHttpRequestArgs'], 10, 2);
        add_action('create_term', [$this, 'capitalizeTermName'], 10, 3);
        add_action('pmxi_saved_post', [$this, 'fetchPostThumbnail'], 12, 3);
        add_action('pmxi_after_xml_import', [$this, 'triggerNextImport'], 10, 2);
        add_filter('wp_all_import_is_post_to_skip', [$this, 'updateImportDate'], 10, 5);
    }

    /**
     * Logs a message to the error log with an Importer prefix.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    private function log($message): void
    {
        error_log('[Importer] ' . $message);
    }

    /**
     * Retrieves the access token from the PCI API.
     *
     * This function sends a POST request to the PCI API authentication URL using the authentication token
     * provided in the environment variables. If the request is successful, it extracts the access token
     * from the response and returns it. If any errors occur during the process, appropriate error messages
     * are logged and an empty string is returned.
     *
     * @return string The access token if successful, or an empty string if an error occurs.
     */
    private function getAccessToken(): string
    {
        $pciApiAuthUrl = $_ENV['PCI_API_AUTH_URL'] ?? '';
        $pciApiAuthToken = $_ENV['PCI_API_AUTH_TOKEN'] ?? '';

        if (!$pciApiAuthUrl || !$pciApiAuthToken) {
            $this->log('PCI_API_AUTH_URL or PCI_API_AUTH_TOKEN is not set in the environment variables.');
            return '';
        }

        $authResponse = wp_remote_post($pciApiAuthUrl . $pciApiAuthToken);

        if (is_wp_error($authResponse)) {
            $this->log('WP All Import auth request failed: ' . $authResponse->get_error_message());
            return '';
        }

        $responseBody = json_decode(wp_remote_retrieve_body($authResponse), true);
        $accessToken = $responseBody['response']['token'] ?? '';

        if (!$accessToken) {
            $this->log('No token found in the auth response.');
            return '';
        }

        return $accessToken;
    }

    /**
     * Retrieves the total results from the PCI API and stores it in a transient.
     * If the request fails or no total is found, it logs an error and returns 0.
     *
     * @return int The total results from the PCI API or 0 on failure.
     */
    private function updateTotalResults(): int
    {
        $this->log('Starting updateTotalResults');
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            $this->log('Access token is empty.');
            return 0;
        }

        $url = getDynamicImportUrl();
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['response']['total'])) {
            set_transient('pci_import_total_results', $body['response']['total'], 60 * 60 * 24);
            $this->log('Total results updated successfully: ' . $body['response']['total']);
            return (int) $body['response']['total'];
        }

        $this->log('No total results found in the response.');
        return 0;
    }

    /**
     * Retrieves the total number of results from the PCI API.
     *
     * This function returns the total results stored in a transient if available,
     * otherwise it updates the total results by calling the PCI API.
     *
     * @return int The total number of results from the PCI API.
     */
    private function getTotalResults(): int
    {
        return (int) get_transient('pci_import_total_results') ?: $this->updateTotalResults();
    }

    /**
     * Modifies the HTTP request arguments to include authorization headers.
     *
     * This function checks if the URL matches the dynamic import URL and
     * adds necessary headers for authorization and content type. The
     * authorization token is retrieved from a transient.
     *
     * @param array  $args The HTTP request arguments.
     * @param string $url  The URL of the request.
     * @return array The modified HTTP request arguments.
     */
    public function modifyHttpRequestArgs($args, $url): array
    {
        $pciApiBaseUrl = $_ENV['PCI_API_BASE_URL'] ?? '';

        if (!$pciApiBaseUrl) {
            $this->log('PCI_API_BASE_URL is not set in the environment variables.');
            return $args;
        }

        if (str_contains($url, $pciApiBaseUrl)) {
            $accessToken = $this->getAccessToken();
            if (empty($accessToken)) {
                $this->log('Access token is empty.');
                return $args;
            }

            $args['headers'] = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ];
        }

        return $args;
    }

    /**
     * Capitalize the first letter of the term name when it is created or edited.
     *
     * @param  int  $termId  The term ID.
     * @param  int  $taxonomyId  The term taxonomy ID.
     */
    public function capitalizeTermName(int $termId, int $taxonomyId): void
    {
        $term = get_term($termId);

        if (is_wp_error($term) || empty($term->name)) {
            $this->log('Term not found or term name is empty.');
            return;
        }

        $termName = strtolower($term->name);

        $capitalizedName = mb_ucfirst($termName);

        if ($termName !== $capitalizedName) {
            wp_update_term($termId, $term->taxonomy, ['name' => $capitalizedName]);
            $this->log('Term name capitalized successfully');
        }
    }

    /**
     * Retrieves the image URL for a given post ID from the PCI API.
     *
     * This function expects the PCI_API_IMAGES_URL environment variable to be set and
     * that the post meta contains a 'upc_code' key with the id to query the API.
     *
     * @param int $postId The post ID to retrieve the image URL for.
     * @return string|null The image URL or null if the request fails or no image is found.
     * @throws Exception If the PCI_API_IMAGES_URL is not set or if the request fails.
     */
    private function getImageUrlFromApi(int $postId): ?string
    {
        $pciApiImagesUrl = $_ENV['PCI_API_IMAGES_URL'] ?? null;
        if (!$pciApiImagesUrl) {
            $this->log('PCI_API_IMAGES_URL is not set in the environment variables.');
            return null;
        }

        $pciId = get_post_meta($postId, 'upc_code', true);
        if (!$pciId) {
            $this->log('No upc_code found in the post meta.');
            return null;
        }

        $response = wp_remote_get(sprintf('%s?code=%d', $pciApiImagesUrl, $pciId));
        if (is_wp_error($response)) {
            $this->log('Error fetching image URL: ' . $response->get_error_message());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($body['success']) || empty($body['response']['data'][0]['url'])) {
            $this->log('Invalid response or no image data found.');
            return null;
        }

        $this->log('Image URL retrieved successfully');
        return $body['response']['data'][0]['url'];
    }

    /**
     * Searches for an attachment in the WordPress media library by filename.
     *
     * This function queries the WordPress database to find the first attachment post ID
     * that has a filename matching the provided string. The search is performed using a
     * SQL LIKE query, allowing partial matches.
     *
     * @param string $filename The filename to search for in the media library.
     * @return string|null The ID of the attachment post if found, or null if no match is found.
     */
    private function searchAttachmentByFilename(string $filename): ?string {
        $this->log('Starting searchAttachmentByFilename for filename: ' . $filename);
        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT post_id
            FROM $wpdb->postmeta
            WHERE meta_key = '_wp_attached_file'
            AND meta_value LIKE %s
            LIMIT 1",
            '%' . $wpdb->esc_like($filename) . '%'
        );

        $attachmentId = $wpdb->get_var($sql);

        if ($attachmentId) {
            $this->log('Attachment found with ID: ' . $attachmentId);
        } else {
            $this->log('No attachment found for filename: ' . $filename);
        }

        return $attachmentId ?: null;
    }

    /**
     * Downloads a file from given URL and uploads it to WordPress media library.
     * Returns ID of the new attachment or false on failure.
     *
     * @param string $imageUrl URL of the file to be downloaded.
     * @return int|false ID of the new attachment or false on failure.
     */
    private function uploadFileByUrl(string $imageUrl): int|false
    {
        $this->log('Starting uploadFileByUrl for URL: ' . $imageUrl);
        // it allows us to use download_url() and wp_handle_sideload() functions
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        // download to temp dir
        $temp_file = download_url($imageUrl);

        if (is_wp_error($temp_file)) {
            $this->log('Error downloading file: ' . $temp_file->get_error_message());
            return false;
        }

        // move the temp file into the uploads directory
        $file = array(
            'name'     => basename($imageUrl),
            'type'     => mime_content_type($temp_file),
            'tmp_name' => $temp_file,
            'size'     => filesize($temp_file),
        );
        $sideload = wp_handle_sideload(
            $file,
            array(
                'test_form'   => false // no needs to check 'action' parameter
            )
        );

        if (!empty($sideload['error'])) {
            $this->log('Error sideloading file: ' . $sideload['error']);
            return false;
        }

        // it is time to add our uploaded image into WordPress media library
        $attachment_id = wp_insert_attachment(
            array(
                'guid'           => $sideload['url'],
                'post_mime_type' => $sideload['type'],
                'post_title'     => basename($sideload['file']),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $sideload['file']
        );

        if (is_wp_error($attachment_id) || !$attachment_id) {
            $this->log('Error inserting attachment: ' . ($attachment_id ? $attachment_id->get_error_message() : 'Unknown error'));
            return false;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        wp_update_attachment_metadata(
            $attachment_id,
            wp_generate_attachment_metadata($attachment_id, $sideload['file'])
        );

        $this->log('File uploaded successfully with attachment ID: ' . $attachment_id);
        return $attachment_id;
    }

    /**
     * Try to find an existing attachment by filename, or upload it from given URL and return its ID.
     *
     * @param string $imageUrl URL of the image to upload if not found.
     * @return int The attachment ID of the image.
     * @throws Exception If the image cannot be uploaded from the given URL.
     */
    private function getAttachmentIdByUrlOrUpload(string $imageUrl): int
    {
        $this->log('Starting getAttachmentIdByUrlOrUpload for URL: ' . $imageUrl);
        $filename = basename($imageUrl);
        $attachmentId = $this->searchAttachmentByFilename($filename);

        if (!$attachmentId) {
            $attachmentId = $this->uploadFileByUrl($imageUrl);
            if (!$attachmentId) {
                throw new Exception('Failed to upload image from URL.');
            }
        }

        $this->log('Attachment ID retrieved or uploaded successfully: ' . $attachmentId);
        return $attachmentId;
    }

    /**
     * Try to fetch a post thumbnail from the API and set it for the given post.
     *
     * @param int $postId The ID of the post to set the thumbnail for.
     * @return void
     * @throws Exception If the image cannot be downloaded from the API.
     */
    public function fetchPostThumbnail(int $postId): void
    {
        $this->log('Starting fetchPostThumbnail for post ID: ' . $postId);
        try {
            $imageUrl = $this->getImageUrlFromApi($postId);
            if (!$imageUrl) {
                $this->log('Failed to retrieve a valid image URL from the API.');
                return;
            }

            $attachmentId = $this->getAttachmentIdByUrlOrUpload($imageUrl);

            if ($attachmentId) {
                set_post_thumbnail($postId, $attachmentId);
            }
        } catch (Exception $e) {
            $this->log('Error fetching post thumbnail: ' . $e->getMessage());
        }
    }

    /**
     * Deletes pci_products that were not imported today.
     *
     * This function retrieves all pci_products that either do not have an 'import_date' meta key
     * or have an 'import_date' that is not equal to today's date. It then deletes each of these posts
     * and logs the deletion.
     *
     * @return void
     */
    private function deletePCIProductsNotImported(): void
    {
        $today = date('d-m-Y');

        $args = [
            'post_type' => 'pci_product',
            'posts_per_page' => -1,
            'meta_query' => [
            'relation' => 'OR',
                [
                    'key' => 'import_date',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => 'import_date',
                    'value' => $today,
                    'compare' => '!=',
                ],
            ],
            'suppress_filters' => true,
        ];

        $products = get_posts($args);

        foreach ($products as $product) {
            wp_delete_post($product->ID, true);
            $this->log("Deleted pci_product post ID: {$product->ID} with title: '{$product->post_title}' not imported today.");
        }
    }

    /**
     * Triggers the next import after the current import is completed.
     *
     * This function is hooked to the 'pmxi_after_xml_import' action and is responsible for triggering
     * the next import after the current import is completed. It checks the import ID and triggers the
     * next import if the import ID is 2 or 3.
     *
     * @param  int  $importId  The ID of the import.
     * @param  object  $import  The import data.
     */
    public function triggerNextImport(int $importId, object $import = null): void
    {
        if (!in_array($importId, [self::IMPORT_ID_FRENCH, self::IMPORT_ID_ENGLISH])) {
            return;
        }

        $homeUrl = get_home_url();
        $options = get_option('PMXI_Plugin_Options');
        $importKey = $options['cron_job_key'] ?? '';
        $maxResults = 250;
        $transientKey = 'pci_import_start_at_';
        $startAt = get_transient($transientKey);
        $startAt = $startAt ? $startAt + $maxResults : 1;

        if (empty($importKey)) {
            $this->log('Import key not found in PMXI_Plugin_Options.');
            return;
        }

        $totalResults = $this->getTotalResults();

        if ($startAt > $totalResults) {
            $url = sprintf('%s/wp-load.php?import_key=%s&import_id=%d&action=cancel', $homeUrl, $importKey, $importId);
            delete_transient($transientKey);

            if ($importId == self::IMPORT_ID_FRENCH) {
                $this->log('French import completed, triggering English import.');
                $this->triggerNextImport(self::IMPORT_ID_ENGLISH);
            } else {
                $this->deletePCIProductsNotImported();
            }

            return;
        } else {
            set_transient($transientKey, $startAt, 60 * 60 * 24);
            $url = sprintf('%s/wp-load.php?import_key=%s&import_id=%d&action=trigger', $homeUrl, $importKey, $importId);
        }

        wp_remote_get(esc_url_raw($url), [
            'sslverify' => !empty($_ENV['SSL_VERIFY']) ? filter_var($_ENV['SSL_VERIFY'], FILTER_VALIDATE_BOOLEAN) : true,
        ]);

        $this->log('Next import triggered for import ID: ' . $importId . ' with startAt: ' . $startAt);
    }

    /**
     * Updates the import date for a given post.
     *
     * @param bool $is_skip Indicates whether the import should be skipped.
     * @param int $import_id The ID of the import process.
     * @param object $current_xml_node The current XML node being processed.
     * @param int $iteration The current iteration of the import process.
     * @param int $post_to_update_id The ID of the post to update.
     *
     * @return bool The updated value of $is_skip.
     */
    public function updateImportDate($is_skip, $import_id, $current_xml_node, $iteration, $post_to_update_id): bool{
        update_post_meta($post_to_update_id, 'import_date', date('d-m-Y'));

        return $is_skip;
    }
}

if (wp_doing_cron() || function_exists('wp_all_import_get_import_id')) {
    new Importer();
}

/**
 * Generates a dynamic import URL to import products from the PCI.
 *
 * This function constructs a URL to import products from the PCI.
 * It uses the base URL and token from environment variables and manages pagination
 * using a transient stored in the WordPress database.
 *
 * @return string The constructed URL or an empty string if environment variables are not set.
 */
function getDynamicImportUrl(): string
{
    $pciApiCustomerProductsURL = $_ENV['PCI_API_CUSTOMER_PRODUCTS_URL'] ?? '';
    $pciApiCustomerCode = (int) $_ENV['PCI_API_CUSTOMER_CODE'] ?? 0;
    $maxResults = 250;

    if (!$pciApiCustomerProductsURL || !$pciApiCustomerCode) {
        error_log('[IMPORTER] PCI_API_CUSTOMER_PRODUCTS_URL or PCI_API_CUSTOMER_CODE is not set in the environment variables.');
        return '';
    }

    $transientKey = "pci_import_start_at_";
    $startAt = get_transient($transientKey);

    if (!$startAt) {
        $startAt = 1;
        set_transient($transientKey, $startAt, 60 * 60 * 24);
    }

    return sprintf('%s?maxResults=%d&startAt=%d&cusCode=%d', $pciApiCustomerProductsURL, $maxResults, $startAt, $pciApiCustomerCode);
}
