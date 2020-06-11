# Docker Image for Sending Email

Docker image for sending emails over SMTP.

Using a docker image means that a system with docker can send emails without installing dependencies on the system itself.

The underlying code is basic PHP that uses PHPMailer to do the hard work.


## Environment Variables

The SMTP config is currently set via environment variables. 

This allows the flexibility of using an env file (with `--env-file=<file>`) and/or individual env arguments 
(with `-e=<envvar>`) when invoking on CLI.

This obviously comes with the benefit of being able to create a container which can be rerun, e.g. for a system notification.


### SMTP Config

```
SMTP_HOST=smtp.ionos.co.uk
SMTP_POST=587
SMTP_USER=noreply@example.co.uk
SMTP_PASS=password
```

### Mail Options

```
MAIL_FROM=noreply@example.co.uk
MAIL_TO=you@example.co.uk
MAIL_SUBJECT=System Mail
```

The email body is currently read from stdin, as the body is expected to be piped in, for instance to send the output from
running a command.


## Running with Docker

```
echo "Hello" | docker run --rm -i --env-file=.env jbuncle/docker-mailer
``


### Real World Example

I created this image so that I could run a regular security audit on system which I didn't want to add mail dependencies to.

Using lynis I can setup a cronjob to run a security audit and email me the results.

```
lynis --no-log --no-colors  audit system | docker run --rm -i --env-file=.env jbuncle/docker-mailer
```