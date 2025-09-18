<?php
session_start();

function flash(string $msg, string $type='success'): void {
    $_SESSION['flash'][] = ['msg'=>$msg, 'type'=>$type];
}

function flashes(): string {
    $out='';
    if (!empty($_SESSION['flash'])) {
        foreach($_SESSION['flash'] as $f){
            $cls = $f['type']==='error' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-green-100 text-green-800 border-green-200';
            $out .= '<div class="p-3 mb-3 rounded-xl border ' . $cls . '">' . htmlspecialchars($f['msg']) . '</div>';
        }
        unset($_SESSION['flash']);
    }
    return $out;
}

function h(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function valid_skill(string $s): bool { return in_array($s, ['Beginner','Intermediate','Advanced'], true); }
