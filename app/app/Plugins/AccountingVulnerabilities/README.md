/api/plugins/vulnerabilities/create/

```json
{
    "vulnerability": {
        "name": "What is Lorem Ipsum?",
        "description": "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
        "bdu": "8251-AF",
        "cve": "GPC-9412",
        "vector": "RED",
        "grade": "9",
        "elimination": "It has survived not only five centuries",
    },
    "softwares": [
        "Making v3.2",
        "Content v2.7.12043",
        "Distribution v75.12"
    ]
}
```

```php
$vulnerability = \App\Plugins\AccountingVulnerabilities\Services\VulnerabilityHelper::create(
    name: "What is Lorem Ipsum?",
    description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
    bdu: "8251-AF",
    cve: "GPC-9412",
    vector: "RED",
    grade: "9",
    elimination: "It has survived not only five centuries",
);

foreach (["Making v3.2", "Content v2.7.12043", "Distribution v75.12"] as $software) {
    \App\Plugins\AccountingVulnerabilities\Services\VulnerabilityHelper::addSoft($vulnerability, $software);
}
```