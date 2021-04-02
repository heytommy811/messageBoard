<?php

namespace App\modules\common;

use App\modules\common\Constants;
use Carbon\Carbon;

use App\exceptions\MessageException;

/**
 * アイコン共通クラス
 */
class IconUtil
{
    /**
     * 指定したディレクトリ、ファイル名のアイコン画像パスを生成します
     */
    public static function getIconFilePath($filename = null)
    {
        return self::getIconPath(Constants::ICON_PATH, $filename);
    }

    public static function getIconFileTempPath($filename = null)
    {
        return self::getIconPath(Constants::ICON_TEMP_PATH, $filename);
    }

    private static function getIconPath($dir, $filename) {
        if (empty($filename)) {
            // アップロード後にはディレクトリとファイル名が取得できるので直接指定する
            return storage_path('app') . '/' . $dir;
        } else {
            return storage_path('app') . '/' . $dir . '/' . $filename;
        }
    }

    /**
     * アイコン画像をbase64形式の文字列で返します
     */
    public static function getBase64png($path) {

        return file_exists($path) ? ('data:image/png;base64,' .base64_encode(file_get_contents($path))): '';
    }

    /**
     * ユーザーのアイコン画像を設定する
     */
    public static function settingIcon($icon_name, $user_id) {
        // アイコン画像をアップロードしている場合、差し替える
        if (!empty($icon_name)) {
            // アップロードしたアイコン画像のパス
            $tmp_icon_file_path = self::getIconFileTempPath($icon_name);
            if (file_exists($tmp_icon_file_path)) {
                $user_icon_file_path = self::getIconFilePath($user_id . '.png');
                rename($tmp_icon_file_path, $user_icon_file_path);
            }
        }
    }

    /**
     * 仮申し込み中のユーザーのアイコンをリネームする
     */
    public static function renameTempIcon($temp_icon_file_name, $rename_icon_file_name) {
        // アイコン画像をアップロードしている場合、差し替える
        if (!empty($temp_icon_file_name)) {
            // アップロードしたアイコン画像のパス
            $tmp_icon_file_path = self::getIconFileTempPath($temp_icon_file_name);
            if (file_exists($tmp_icon_file_path)) {
                $rename_path = self::getIconFileTempPath($rename_icon_file_name . '.png');
                rename($tmp_icon_file_path, $rename_path);
            }
        }
    }

    /**
     * アイコン画像のURLを取得する
     */
    public static function getIconUrl($user_id) {
        $icon_path = self::getIconFilePath($user_id.'.png');
        if (file_exists($icon_path)) {
            return sprintf(Constants::ICON_FILE_PATH, $user_id);
        } else {
            // アイコン画像が未登録の場合
            return sprintf(Constants::ICON_FILE_PATH, 'default');
        }
    }

    /**
     * アイコン画像の切り抜き処理を行う
     */
    public static function saveCroppingIcon($request) {

        $result = '';
        // 画像ファイルがポストされている場合のみ処理を行う
        if (!is_null($request->file)) {

            $file_path = $request->file->store(Constants::ICON_TEMP_PATH);
            // アップロードした画像のパス
            $tmp_icon_path = IconUtil::getIconFileTempPath(basename($file_path));

            // cropperのクリップ情報
            $width = $request->input('width');
            $height = $request->input('height');
            $x = $request->input('x');
            $y = $request->input('y');

            // 拡張子を取得する
            $exif_data = null;
            $path_info = pathinfo($tmp_icon_path);
            switch($path_info['extension']) {
                case 'jpg':
                case 'jpeg':
                    $resource_image = imagecreatefromjpeg($tmp_icon_path);
                    // 画像の向きを取得する
                    $exif_data = exif_read_data($tmp_icon_path);
                    break;
                case 'png':
                    $resource_image = imagecreatefrompng($tmp_icon_path);
                    break;
                case 'gif':
                    $resource_image = imagecreatefromgif($tmp_icon_path);
                    break;
                default:
                    throw new MessageException('未対応のファイル形式です。');
                    break;
                }

            // 画像の向きを取得する
            if ($exif_data != null && array_key_exists('Orientation', $exif_data)) {
                $orientation = $exif_data['Orientation'];
                if ($orientation == 6){
                    // 時計回りに90度回転しているため、時計回りに270度回転する
                    $resource_image = imagerotate($resource_image, 270, 0, 0);
                } else if ($orientation == 3){
                    // 180回転しているため、時計回りに180度回転する
                    $resource_image = imagerotate($resource_image, 180, 0, 0);
                } else if ($orientation == 8){
                    // 反時計回りに90度回転しているため、時計回りに90度回転する
                    $resource_image = imagerotate($resource_image, 90, 0, 0);
                }
            }

            // 画像を指定の位置から指定の大きさ（サイズ）に切り抜く
            $crop_image = imagecrop($resource_image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);

            if ($crop_image !== FALSE) {
                // 成功した場合、画像を上書きする
                switch($path_info['extension']) {
                    case 'jpg':
                    case 'jpeg':
                        $cropped_icon_path = IconUtil::getIconFileTempPath($path_info['filename'] . '.png');
                        imagepng($crop_image, $cropped_icon_path);
                        // 元ファイルを削除
                        unlink($tmp_icon_path);
                        break;
                    case 'png':
                        $cropped_icon_path = $tmp_icon_path;
                        imagepng($crop_image, $tmp_icon_path);
                        break;
                    case 'gif':
                        $cropped_icon_path = IconUtil::getIconFileTempPath($path_info['filename'] . '.png');
                        imagegif($crop_image, $cropped_icon_path);
                        // 元ファイルを削除
                        unlink($tmp_icon_path);
                        break;
                    }
                imagedestroy($crop_image);
                // アイコン画像を設定する
                $result = basename($cropped_icon_path);
            }
        }
        return $result;
    }
}
