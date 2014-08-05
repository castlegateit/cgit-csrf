<?php

class cgit_crsf
{

    // ------------------------------------------------------------------------

    /**
     * Key to use then storing tokens in the $_SESSION array
     *
     * @var string
     */
    public $session_key = '__csrf';

    /**
     * Length of the generated CSRF tokens
     *
     * @var integer
     */
    public $token_length = 128;

    /**
     * Key to use for posting CSRF tokens
     *
     * @var integer
     */
    public $post_key = '__csrf';

    // ------------------------------------------------------------------------

    /**
     * Generate a CSRF token if one does not exist or is of unexpected length.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return void
     */
    public function __construct()
    {
        if (!isset($_SESSION[$this->session_key])
            || strlen($this->get_token()) != $this->token_length) 
        {
            $this->generate_new_token();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Get the current CSRF token. If it does not exist or is of unexpected
     * length, generate a new one first.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return string
     */
    public function get_token()
    {
        if (!isset($_SESSION[$this->session_key])
            || strlen($_SESSION[$this->session_key]) != $this->token_length) 
        {
            $this->generate_new_token();
        }

        return $_SESSION[$this->session_key];
    }

    // ------------------------------------------------------------------------

    /**
     * Get the current CSRF token. If it does not exist or is of unexpected
     * length, generate a new one first and then returns the token wrapped in
     * the required HTML tags.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return string
     */
    public function get_html()
    {
        if (!isset($_SESSION[$this->session_key])
            || strlen($this->get_token()) != $this->token_length) 
        {
            $this->generate_new_token();
        }

        $html = '<input type="hidden" name="' . $this->post_key. '" ';
        $html.= 'value="' . $_SESSION[$this->session_key] . '" />';

        return $html;
    }

    // ------------------------------------------------------------------------

    /**
     * Get the array key for posted tokens.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return string
     */
    public function get_post_key()
    {
        return $this->post_key;
    }

    // ------------------------------------------------------------------------

    /**
     * Get the session array key used to store the current token.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return string
     */
    public function get_session_key()
    {
        return $this->session_key;
    }

    // ------------------------------------------------------------------------

    /**
     * Compare the posted CSRF token with the value stored in the session. If a
     * mismatch is detected, a new token is generated automatically.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return string
     */
    public function check_token()
    {
        if (isset($_POST[$this->post_key])
            && isset($_SESSION[$this->session_key])
            && $_POST[$this->post_key] == $_SESSION[$this->session_key])
        {
            return true;
        }

        // Else
        $this->generate_new_token();
        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Generate and assign a new CSRF token to the session variable.
     *
     * @author Andy Reading <andy@castlegateit.co.uk>
     *
     * @return void
     */
    private function generate_new_token()
    {
       for ($i = -1; $i <= ($this->token_length * 4); $i++)
       {
            $bytes = openssl_random_pseudo_bytes($i);
            $hex   = bin2hex($bytes);

            if (strlen($hex) == $this->token_length)
            {
                break;
            }
        }

        $_SESSION[$this->session_key] = $hex;
    }

    // ------------------------------------------------------------------------

}

/* END of file */
