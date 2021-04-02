<?php

use Illuminate\Database\Seeder;

class sm_sys_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sm_sys')->insert([
            [
                'key' => 'mail_mode',
                'value' => '1',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'entry_mail_from',
                'value' => 'heytommy811@gmail.com',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'entry_mail_subject',
                'value' => '【MessageBoard】アカウント登録確認のご案内',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'entry_mail_message_template_before',
                'value' => 'この度はMessageBoardのアカウントを作成いただき誠にありがとうございます。,下記URLをクリックし、登録を完了してください。',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'entry_mail_message_template_after',
                'value' => '上記URLの有効期限は24時間となります。,有効期限を過ぎた場合は大変お手数ですが、再度アカウント作成行ってください。,,※本メールはシステムの自動返信のため、返信はできません。,お問い合わせは下記メールアドレスへお願いします。,,-------------------------,MessageBoard事務局,メールアドレス:toiawase@message.board.com,-------------------------',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'reset_password_mail_from',
                'value' => 'heytommy811@gmail.com',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'reset_password_mail_subject',
                'value' => '【MessageBoard】パスワードリセットのご案内',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'reset_password_mail_message_template_before',
                'value' => 'この度はMessageBoardをご利用いただき誠にありがとうございます。,パスワードのリセット方法についてご案内いたします。,下記URLLをクリックし、新しいパスワードをご登録ください。',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'reset_password_mail_message_template_after',
                'value' => '※上記URLの有効期限は24時間となります。,有効期限を過ぎた場合は大変お手数ですが、再度パスワードリセットを行ってください。,,※本メールはシステムの自動返信のため、返信はできません。,お問い合わせは下記メールアドレスへお願いします。,,-------------------------,MessageBoard事務局,メールアドレス:toiawase@message.board.com,-------------------------',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'message_login_fail',
                'value' => 'メールアドレスまたはパスワードが正しくありません。',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'messsage_login_lock',
                'value' => 'アカウントをロックしています。しばらくしてから再度ログインしてください。',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'message_account_lock',
                'value' => 'アカウント凍結中です。',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'limit_login_fail_count',
                'value' => '4',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'limit_login_fail_minutes',
                'value' => '10',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'limit_login_lock_count',
                'value' => '3',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
            [
                'key' => 'limit_login_lock_minutes',
                'value' => '30',
                'created_at' => new DateTime(),  // timestamp type
                'updated_at' => new DateTime(),  // timestamp type
            ],
        ]);
    }
}
