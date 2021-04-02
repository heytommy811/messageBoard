<?php

namespace App\modules\common;

/**
 * メールユーティリティ
 */
class MailUtil
{
    /**
     * メール送信処理
     */
    public static function sendMail($from, $to, $subject, $message) {
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        mb_send_mail($to, $subject, $message, 'From: ' . $from);
    }

    /**
     * メッセージ本文を作成する
     */
    public static function createMailMessage($before, $url, $after) {
        $message = '';
        // URLより前の文章
        if (!empty($before)) {
            // カンマ区切りで改行していく
            $temp_array = explode(',', $before);
            foreach($temp_array as $tmp) {
                $message .= $tmp . "\r\n";
            }
        }

        // 登録完了URL
        $message .= "\r\n" . $url . "\r\n";
        
        // URLの後の文章
        if (!empty($after)) {
            $temp_array = explode(',', $after);
            foreach($temp_array as $tmp) {
                $message .= $tmp . "\r\n";
            }
        }
        return $message;
    }
}
