<?php
declare(strict_types=1);
require_once __DIR__.'/auth.php';
function qr_payload(int $id, int $version, string $validUntil): string { $data=$id.'|'.$version.'|'.$validUntil; return base64url_encode($data.'|'.hash_hmac('sha256',$data,qr_secret())); }
function base64url_encode(string $value): string { return rtrim(strtr(base64_encode($value), '+/', '-_'), '='); }
function base64url_decode(string $value): string|false { return base64_decode(strtr($value.'===', '-_', '+/'), true); }
function verify_qr(string $token): ?array { $raw=base64url_decode($token); if($raw===false)return null; $parts=explode('|',$raw); if(count($parts)!==4)return null; [$id,$version,$until,$sig]=$parts; if(!ctype_digit($id)||!ctype_digit($version)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$until))return null; $data=$id.'|'.$version.'|'.$until; if(!hash_equals(hash_hmac('sha256',$data,qr_secret()),$sig)||$until<date('Y-m-d'))return null; global $pdo; $s=$pdo->prepare("SELECT id,nome,matricula,status_financeiro,qr_version,qr_valid_until FROM usuarios WHERE id=? AND perfil='ASSOCIADO' LIMIT 1");$s->execute([(int)$id]);$u=$s->fetch();return $u&&((int)$u['qr_version']===(int)$version)&&$u['qr_valid_until']>=$until?$u:null; }
