# wlib/http-client

Classe PHP pour effectuer des requêtes HTTP.

## Installation

```shell
composer require wlib/http-client
```

Nécessite l'extension `cURL`.

## Classes disponibles

## \wlib\Http\Client\Http

Classe purement statique pour effectuer des requêtes HTTP.

### Methodes disponibles

```php
public static function get(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function post(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function put(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function patch(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function delete(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function head(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function options(string $sUrl, array|string $mData = [], array $aMore = []): array;
public static function download(string $sUrl, mixed $mFile, array|string $mData = [], array $aMore = []): array;
public static function request(string $sUrl, string $sMethod = 'get', array|string $mData = [], array $aMore = []): array;
public static function multipartBuildQuery(array $aFields, string $sBoundary = ''): array;
```