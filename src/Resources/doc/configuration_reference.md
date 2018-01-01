```yaml
# Default configuration for extension with alias: "spiechu_symfony_commons"
spiechu_symfony_commons:
  get_method_override:
    enabled: false
    listener_service_id: spiechu_symfony_commons.event_listener.get_method_override_listener
    query_param_name: _method
    allow_methods_override:
      - DELETE
      - POST
      - PUT
  response_schema_validation:
    enabled: false
    throw_exception_when_format_not_found: true
    failed_schema_check_listener_service_id: spiechu_symfony_commons.event_listener.failed_schema_check_listener
    disable_json_check_schema_subscriber: false
    disable_xml_check_schema_subscriber: false
  api_versioning:
    enabled: false
    versioned_view_listener: false
```
