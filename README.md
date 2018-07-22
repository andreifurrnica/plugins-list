##Enable module
`php bin/magento module:enable Andrei_PluginsList`

## Usage
### List all plugins 
`php bin/magento af:list-plugins`

### Options
1. List only vendor plugins: `--vendor-only`
1. Exclude vendor plugins: `--exclude-vendor`
1. Filter by type (intercepted class): `--type="<class>"` (Quotes around class name MUST be present)