<?php

require_once __DIR__ . '/../common.php';

function assertTrue($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$issued = issueSessionToken();
assertTrue(is_array($issued), 'issueSessionToken should return an array');
assertTrue(strlen($issued['token']) === 64, 'token should be 64 characters');
assertTrue(preg_match('/^[a-f0-9]{64}$/', $issued['token']) === 1, 'token should be hex');
assertTrue(verifySessionToken($issued['token'], $issued['expiresAt']), 'fresh token should verify');
assertTrue(!verifySessionToken('invalid-token', $issued['expiresAt']), 'invalid token should fail');
assertTrue(!verifySessionToken($issued['token'], date('Y-m-d H:i:s', time() - 60)), 'expired token should fail');

echo 'session helper tests passed' . PHP_EOL;
