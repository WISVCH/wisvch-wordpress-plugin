<?php

namespace WISVCH\CHoice;

/**
 * Initialize CH member portal.
 *
 * @package WISVCH\Portal
 */
class Member
{
    const ADMINISTRATOR_LDAP_GROUPS = ['w3cie'];

    const EDITOR_LDAP_GROUPS = ['website'];

    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {

        // Prevent dashboard access
        // Source: https://nl.wordpress.org/plugins/remove-dashboard-access-for-non-admins
        add_action('admin_init', [__CLASS__, 'admin_init']);
        add_action('init', [__CLASS__, 'init']);

        // Process CH Connect log-ins
        add_action('openid-connect-generic-user-create', [__CLASS__, 'ch_connect_user_creation'], 10, 2);

        // Update user metadata if necessary
        add_action('openid-connect-generic-update-user-using-current-claim', [__CLASS__, 'ch_connect_user_claim_update'], 10, 2);

        // Store last login data
        add_action('wp_login', [__CLASS__, 'set_last_login']);
    }

    /**
     * Change WP_User role to `ch_member`.
     *
     * @param $user
     * @param $user_claim
     */
    static function ch_connect_user_creation(\WP_User $user, $user_claim)
    {

        // Add role `ch_member`
        $user->add_role('ch_member');

        // Update user meta with user claim data
        self::ch_connect_user_claim_update($user, $user_claim);
    }

    static function ch_connect_user_claim_update(\WP_User $user, $claim)
    {

        // Skip function if no LDAP group access provided by user
        if (! isset($claim['ldap_groups']) || ! is_array($claim['ldap_groups'])) {
            return;
        }

        // Administrator
        if (count(array_intersect(self::ADMINISTRATOR_LDAP_GROUPS, $claim['ldap_groups'])) > 0) {

            // Add
            if (! $user->has_role('administrator')) {
                $user->add_role('administrator');
            }
        } else {

            // Remove
            if ($user->has_role('administrator')) {
                $user->remove_role('administrator');
            }
        }

        // Editor
        if (count(array_intersect(self::EDITOR_LDAP_GROUPS, $claim['ldap_groups'])) > 0) {

            // Add
            if (! $user->has_role('editor')) {
                $user->add_role('editor');
            }
        } else {

            // Remove
            if ($user->has_role('editor')) {
                $user->remove_role('editor');
            }
        }
    }

    /**
     * Add custom CH Member role.
     *
     * @return bool Success flag.
     */
    static function register_role()
    {
        if (is_admin()) {

            // Get WP_Roles object
            $roles = wp_roles();

            // Get CH Member role
            $role = $roles->get_role("ch_member");

            if ($role === null) {
                $role = add_role('ch_member', 'CH Member', ['read']);
            }

            // Set read capability (if not yet set)
            if (! $role->has_cap('read')) {
                $role->add_cap('read');
            }

            return true;
        }

        return false;
    }

    /**
     * Check whether or not the user's only role is `ch_member`.
     *
     * @return bool
     */
    static function _is_ch_member_exclusively()
    {
        // Get current user
        $user = wp_get_current_user();

        /**
         * @see is_user_logged_in()
         */
        if ($user->exists()) {
            return count($user->roles) === 1 && reset($user->roles) === 'ch_member';
        }

        return false;
    }

    /**
     * Check if a user has role `ch_member`.
     *
     * @return bool
     */
    static function _is_ch_member()
    {
        // Get current user
        $user = wp_get_current_user();

        /**
         * @see is_user_logged_in()
         */
        if ($user->exists()) {
            return count($user->roles) > 0 && in_array('ch_member', $user->roles);
        }

        return false;
    }

    /**
     * Dashboard redirect.
     */
    static function admin_init()
    {

        if (! (defined('DOING_AJAX') && DOING_AJAX) && self::_is_ch_member_exclusively()) {

            // Redirect to user portal
            wp_redirect(Init::getUrl());
            exit;
        }
    }

    /**
     * Hide toolbar items.
     */
    static function init()
    {
        if (self::_is_ch_member_exclusively()) {

            // Disable admin bar.
            show_admin_bar(false);
        }
    }

    /**
     * Store user's last login time.
     *
     * @param $login
     */
    static function set_last_login($login)
    {
        $user = get_user_by('login', $login);

        //add or update the last login value for logged in user
        update_user_meta($user->ID, 'last_login', current_time('mysql', true));
    }

    /**
     * @todo Make singleton.
     */
    static function get_user_claim(\WP_User $user = null)
    {

        if (empty($user)) {

            // Get current user
            $user = wp_get_current_user();
        }

        // Load user claim
        $claim = get_user_meta($user->ID, 'openid-connect-generic-last-user-claim', true);

        if (empty($claim)) {
            return [];
        } else {
            return $claim;
        }
    }
}
