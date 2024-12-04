<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'msg' => '无效的请求方法']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'msg' => '未找到上传文件']);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'msg' => '文件上传失败']);
    exit;
}

$headers = [
    'accept: application/json, text/plain, */*',
    'accept-encoding: gzip, deflate, br, zstd',
    'accept-language: zh-CN,zh;q=0.9',
    'origin: https://mofang-m-pub.ddxq.mobi',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
];

$ch = curl_init('https://pubgw.api.ddxq.mobi/solar-mform-service/file/v1/public/upload');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$cfile = new CURLFile(
    $file['tmp_name'],
    $file['type'],
    $file['name']
);
$data = ['file' => $cfile];

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'msg' => '服务器请求失败']);
    exit;
}

$result = json_decode($response, true);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'url' => $result['data']['url']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'msg' => $result['msg'] ?? '上传失败'
    ]);
}
