<?php

namespace Core\Services\SMS;

class SMSMessage
{

    /**
     * The subdomain.
     *
     * @var string
     */
    protected $subdomain;

    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * Receivers of the message.
     * 
     * @var array<string>|string
     */
    public $receivers;

    /**
     * The driver.
     *
     * @var string
     */
    public $driver = "log";

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     * @param  string  $actionType
     * @return void
     */
    public function __construct(
        ?string $content = null,
        ?string $subdomain = null,
        ?string $driver = "log"
    ) {
        $this->content = $content;
        $this->subdomain = $subdomain;
        $this->driver = $driver;
    }

    /**
     * Set the receivers of the message.
     *
     * @param  array<string>|string  $receivers
     * @return self
     */
    public function to($receivers): self
    {
        $this->receivers = $receivers;

        return $this;
    }

    /**
     * Set the subdomain.
     *
     * @param  string  $subdomain
     * @return self
     */
    public function setSubdomain(string $subdomain): self
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get the message content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content ?? '';
    }

    /**
     * Set the message line.
     *
     * @param string $line
     * @return self
     */
    public function line($line)
    {
        $this->content .= $line . "\n";

        return $this;
    }

    /**
     * Get the receivers of the message.
     *
     * @return array<string>|string
     */
    public function getReceivers()
    {
        return $this->receivers;
    }

    /**
     * Get the subdomain.
     *
     * @return string
     */
    public function getSubdomain(): string
    {
        return $this->subdomain;
    }

    /**
     * Get the driver to send the message.
     *
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the driver.
     *
     * @param  string  $driver
     * @return self
     */
    public function setDriver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get the array representation of the message.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'driver' => $this->driver,
        ];
    }

    /**
     * Get the JSON serializable representation of the message.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get the string representation of the message.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
