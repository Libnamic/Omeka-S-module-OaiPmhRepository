<?php
namespace OaiPmhRepository;

$services = $serviceLocator;
$settings = $services->get('Omeka\Settings');
$connection = $services->get('Omeka\Connection');
$config = require dirname(dirname(__DIR__)) . '/config/module.config.php';
$defaultSettings = $config[strtolower(__NAMESPACE__)]['config'];

if (version_compare($oldVersion, '0.3', '<')) {
    $connection = $serviceLocator->get('Omeka\Connection');
    $sql = <<<'SQL'
ALTER TABLE oai_pmh_repository_token CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE verb verb VARCHAR(190) NOT NULL, CHANGE metadata_prefix metadata_prefix VARCHAR(190) NOT NULL, CHANGE `cursor` `cursor` INT NOT NULL, CHANGE `set` `set` INT DEFAULT NULL;
DROP INDEX expiration ON oai_pmh_repository_token;
CREATE INDEX IDX_E9AC4F9524CD504D ON oai_pmh_repository_token (expiration);
SQL;
    $connection->exec($sql);

    $settings->set('oaipmhrepository_name', $settings->get('oaipmh_repository_name',
        $settings->get('installation_title')));
    $settings->set('oaipmhrepository_namespace_id', $settings->get('oaipmhrepository_namespace_id',
        $this->getServerNameWithoutProtocol($serviceLocator)));
    $settings->set('oaipmhrepository_expose_media', $settings->get('oaipmh_repository_namespace_expose_files',
        $defaultSettings['oaipmhrepository_expose_media']));
    $settings->set('oaipmhrepository_list_limit',
        $defaultSettings['oaipmhrepository_list_limit']);
    $settings->set('oaipmhrepository_token_expiration_time',
        $defaultSettings['oaipmhrepository_token_expiration_time']);

    $settings->delete('oaipmh_repository_name');
    $settings->delete('oaipmh_repository_namespace_id');
    $settings->delete('oaipmh_repository_namespace_expose_files');
    $settings->delete('oaipmh_repository_record_limit');
    $settings->delete('oaipmh_repository_list_limit');
    $settings->delete('oaipmh_repository_expiration_time');
    $settings->delete('oaipmh_repository_token_expiration_time');
}

if (version_compare($oldVersion, '0.3.1', '<')) {
    $settings->set('oaipmhrepository_global_repository',
        $defaultSettings['oaipmhrepository_global_repository']);
    $settings->set('oaipmhrepository_by_site_repository', 'item_set');
    $settings->set('oaipmhrepository_oai_set_format',
        $defaultSettings['oaipmhrepository_oai_set_format']);
    $settings->set('oaipmhrepository_human_interface',
        $defaultSettings['oaipmhrepository_human_interface']);
    $settings->set('oaipmhrepository_hide_empty_sets',
        $defaultSettings['oaipmhrepository_hide_empty_sets']);
}

if (version_compare($oldVersion, '3.2.2', '<')) {
    $connection = $serviceLocator->get('Omeka\Connection');
    $sql = <<<'SQL'
ALTER TABLE oai_pmh_repository_token CHANGE `set` `set` VARCHAR(190) DEFAULT NULL;
SQL;
    $connection->exec($sql);

    $settings->set('oaipmhrepository_append_identifier_global',
        $defaultSettings['oaipmhrepository_append_identifier_global']);
    $settings->set('oaipmhrepository_append_identifier_site',
        $defaultSettings['oaipmhrepository_append_identifier_site']);
}

if (version_compare($oldVersion, '3.3.0', '<')) {
    $settings->set('oaipmhrepository_metadata_formats',
        $defaultSettings['oaipmhrepository_metadata_formats']);
    $settings->set('oaipmhrepository_generic_dcterms',
        $defaultSettings['oaipmhrepository_generic_dcterms']);
    $settings->set('oaipmhrepository_mets_data_item',
        $defaultSettings['oaipmhrepository_mets_data_item']);
    $settings->set('oaipmhrepository_mets_data_media',
        $defaultSettings['oaipmhrepository_mets_data_media']);
}
