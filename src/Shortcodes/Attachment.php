<?php

namespace WISVCH\Shortcodes;

/**
 * Attachment card shortcode.
 *
 * @package WISVCH\Shortcodes
 */
class Attachment
{
    const SHORTCODE_NAME = "wisv_attachment";

    /**
     * Render shortcode.
     *
     * @param $atts Shortcode attributes.
     */
    static function do_shortcode($atts, $content)
    {

        // Merge atts with defaults
        $atts = shortcode_atts([
            'id' => '0',
        ], $atts, self::SHORTCODE_NAME);

        // Get URL
        $attachment_url = wp_get_attachment_url($atts['id']);

        // Bail if attachment does not exist.
        if ($attachment_url === false) {
            return;
        }

        // Get title
        $attachment_title = empty($content) ? get_the_title($atts['id']) : $content;

        // Get path and filesize
        $absurl = get_attached_file($atts['id']);
        $attachment_filesize = ! empty($absurl) ? filesize($absurl) : false;

        // Get file type
        $filetype_check = wp_check_filetype($absurl);
        $attachment_filetype = empty($filetype_check['ext']) ? false : $filetype_check['ext'];

        // Render attachment card
        return self::_render($attachment_title, $attachment_url, $attachment_filesize, $attachment_filetype);
    }

    /**
     * Render attachment card.
     *
     * @param $title Attachment title.
     * @param $url Attachment url.
     * @param int|bool $size (optional) Attachment filesize.
     * @param string|bool $type (optional) Attachment file type.
     * @return string HTML attachment card.
     */
    private static function _render($title, $url, $size = false, $type = false)
    {

        ob_start();
        ?>
        <a class="attachment-card <?=self::_getFiletypeClass($type)?>" href="<?=esc_url($url)?>" rel="external">
            <article>
                <h6><?=esc_html($title)?></h6>
                <?php if ($size !== false && $type !== false) { ?>
                    <p class="filesize"><?=strtoupper(esc_html($type));?>, &shy;<?=size_format($size, 1)?></p>
                <?php } ?>
            </article>
        </a>
        <?php
        return ob_get_clean();
    }

    private static function _getFiletypeClass($type)
    {

        switch ($type) {
            case 'pdf':
                return 'ch-file-pdf-o';
                break;

            case 'zip':
            case 'rar':
            case 'tar':
            case '7z':
            case 'gz':
                return 'ch-file-archive-o';
                break;

            case 'xls':
            case 'xlsx':
            case 'ods':
                return 'ch-file-excel-o';
                break;

            case 'doc':
            case 'docx':
            case 'odt':
                return 'ch-file-word-o';
                break;

            default:
                return 'ch-file-o';
                break;
        }
    }
}
