#!/usr/bin/perl -w
##############################################################################
# FormMail                        Version 1.9s-p7                            #
# Copyright 1995-2001 Matt Wright mattw@worldwidemart.com                    #
# Created 06/09/95                Last Modified 02/24/02 00:34:00 PST        #
# Matt's Script Archive, Inc.:    http://www.worldwidemart.com/scripts/      #
# Enhanced Security Version:	  ftp://ftp.monkeys.com/pub/formmail/        #
##############################################################################
# COPYRIGHT NOTICE                                                           #
# Copyright 1995-2001 Matthew M. Wright  All Rights Reserved.                #
#                                                                            #
# FormMail may be used and modified free of charge by anyone so long as this #
# copyright notice and the comments above remain intact.  By using this      #
# code you agree to indemnify Matthew M. Wright from any liability that      #
# might arise from its use.                                                  #
#                                                                            #
# Selling the code for this program without prior written consent is         #
# expressly forbidden.  In other words, please ask first before you try and  #
# make money off of my program.                                              #
#                                                                            #
# Obtain permission before redistributing this software over the Internet or #
# in any other medium.	In all cases copyright and header must remain intact #
##############################################################################
# ACCESS CONTROL FIX: Peter D. Thompson Yezek                                #
#                     http://www.securityfocus.com/archive/1/62033           #
##############################################################################
# MULTIPLE SECURITY FIXES: Ronald F. Guilmette; February 16, 2002            #
#############################################################################
# Define Variables                                                           #
#	 Detailed Information Found In README File.                          #

# $mailprog defines the location of your sendmail program on your unix       #
# system.  (For FreeBSD, change this to "/usr/sbin/sendmail".)               #

$mailprog = '/usr/sbin/sendmail';

# $mail_admin defines the e-mail address that should be used as the          #
# SMTP envelope sender address.  This will be the address to which           #
# FormMail-generated messages will be bounced if they are ever found         #
# to be undeliverable for any reason.  NOTE:  This should be some            #
# e-mail address that is ROUTINELY and FREQUENTLY monitored.                 #

# NOTE! NOTE NOTE!  RFC 2821 absolutely _requires_ every mail server to
# have a working postmaster e-mail address.  If you don't have one then
# for God's sake GET ONE!  This means YOU!

$mail_admin = "postmaster";

# @recipient_addresses defines the e-mail addresses that e-mail can          #
# be sent to.  This must be filled in correctly to prevent SPAM and allow    #
# valid addresses to receive e-mail.  Read the documentation to find out how #
# this variable works!!!  It is EXTREMELY IMPORTANT.                         #

@recipient_addresses = ('ccclerk@communicomm.com');

# ACCESS CONTROL FIX: Peter D. Thompson Yezek                                #
# @valid_ENV allows the sysadmin to define what environment variables can    #
# be reported via the env_report directive.  This was implemented to fix     #
# the problem reported at http://www.securityfocus.com/bid/1187              #

@valid_ENV = ('REMOTE_HOST','REMOTE_ADDR','REMOTE_USER','HTTP_USER_AGENT');

# Done                                                                       #
##############################################################################
# Retrieve Date
&get_date;

# Parse Form Contents
&parse_form;

# Check Required Fields
&check_required;

# Send E-Mail
&send_mail;

# Return HTML Page or Redirect User
&return_html;

sub get_date {

    # Define arrays for the day of the week and month of the year.           #
    @days   = ('Sunday','Monday','Tuesday','Wednesday',
               'Thursday','Friday','Saturday');
    @months = ('January','February','March','April','May','June','July',
	         'August','September','October','November','December');

    # Get the current time and format the hour, minutes and seconds.  Add    #
    # 1900 to the year to get the full 4 digit year.                         #
    ($sec,$min,$hour,$mday,$mon,$year,$wday) = (localtime(time))[0,1,2,3,4,5,6];
    $time = sprintf("%02d:%02d:%02d",$hour,$min,$sec);
    $year += 1900;

    # Format the date.                                                       #
    $date = "$days[$wday], $months[$mon] $mday, $year at $time";

}

sub parse_form {

    # Define the configuration associative array.                            #
    %Config = ('recipient','',          'subject','',
               'email','',              'realname','',
               'redirect','',           'bgcolor','',
               'background','',         'link_color','',
               'vlink_color','',        'text_color','',
               'alink_color','',        'title','',
               'sort','',               'print_config','',
               'required','',           'env_report','',
               'return_link_title','',  'return_link_url','',
               'print_blank_fields','', 'missing_fields_redirect','');

    # Determine the form's REQUEST_METHOD (GET or POST) and split the form   #
    # fields up into their name-value pairs.  If the REQUEST_METHOD was      #
    # not GET or POST, send an error.                                        #
    if ($ENV{'REQUEST_METHOD'} eq 'GET') {
        # Split the name-value pairs
        @pairs = split(/&/, $ENV{'QUERY_STRING'});
    }
    elsif ($ENV{'REQUEST_METHOD'} eq 'POST') {
        # Get the input
        read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});
 
        # Split the name-value pairs
        @pairs = split(/&/, $buffer);
    }
    else {
        &error('request_method');
    }

    # For each name-value pair:                                              #
    foreach $pair (@pairs) {

        # Split the pair up into individual variables.                       #
        local($name, $value) = split(/=/, $pair);
 
        # Decode the form encoding on the name and value variables.          #
        $name =~ tr/+/ /;
        $name =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

	# Avoid any and all cross-site scripting security holes by re-coding
	# any < or > or " characters into their HTML equivalents.
	$name =~ s/</\&lt;/g;
	$name =~ s/>/\&gt;/g;
	$name =~ s/"/\&quot;/g;

        $value =~ tr/+/ /;
        $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

	# Avoid any and all cross-site scripting security holes by re-coding
	# any < or > or " characters into their HTML equivalents.
	$value =~ s/</\&lt;/g;
	$value =~ s/>/\&gt;/g;
	$value =~ s/"/\&quot;/g;

        # If the field name has been specified in the %Config array, it will #
        # return a 1 for defined($Config{$name}}) and we should associate    #
        # this value with the appropriate configuration variable.  If this   #
        # is not a configuration form field, put it into the associative     #
        # array %Form, appending the value with a ', ' if there is already a #
        # value present.  We also save the order of the form fields in the   #
        # @Field_Order array so we can use this order for the generic sort.  #
        if (defined($Config{$name})) {
            $Config{$name} = $value;
        }
        else {
            if ($Form{$name} && $value) {
                $Form{$name} = "$Form{$name}, $value";
            }
            elsif ($value) {
                push(@Field_Order,$name);
                $Form{$name} = $value;
            }
        }
    }

    # The next six lines remove any extra spaces or new lines from the       #
    # configuration variables, which may have been caused if your editor     #
    # wraps lines after a certain length or if you used spaces between field #
    # names or environment variables.                                        #
    $Config{'required'} =~ s/(\s+|\n)?,(\s+|\n)?/,/g;
    $Config{'required'} =~ s/(\s+)?\n+(\s+)?//g;
    $Config{'env_report'} =~ s/(\s+|\n)?,(\s+|\n)?/,/g;
    $Config{'env_report'} =~ s/(\s+)?\n+(\s+)?//g;
    $Config{'print_config'} =~ s/(\s+|\n)?,(\s+|\n)?/,/g;
    $Config{'print_config'} =~ s/(\s+)?\n+(\s+)?//g;

    # Split the configuration variables into individual field names.         #
    @Required = split(/,/,$Config{'required'});
    @Env_Report = split(/,/,$Config{'env_report'});
    @Print_Config = split(/,/,$Config{'print_config'});

    # ACCESS CONTROL FIX: Only allow ENV variables in @valid_ENV in          #
    # @Env_Report for security reasons.                                      #
    foreach $env_item (@Env_Report) {
        foreach $valid_item (@valid_ENV) {
            if ( $env_item eq $valid_item ) { push(@temp_array, $env_item) }
        }
    } 
    @Env_Report = @temp_array;
}

sub check_required {

    # Localize the variables used in this subroutine.                        #
    local($require, @error);

    $Config{'recipient'} = "" unless ($Config{'recipient'});

    # Removed any poisoned nul bytes that may be present in the recipient CGI
    # parameter, just in case.  They might cause wacky behavior from some
    # mail servers.  See http://www.wiretrip.net/rfp/p/doc.asp/i2/d6.htm

    $Config{'recipient'} =~ tr /\0//d if ($Config{'recipient'});

    # Clean up the recipient CGI parameter further by replacing
    # all sequences of whitespace characters (including any possible
    # carriage returns and newlines) with a single space character.

    $Config{'recipient'} =~ s/\s+/ /gs if ($Config{'recipient'});

    # This block of code ensures that each comma-delimited substring of
    # the CGI recipient parameter ends with either an authorized domain
    # name or an authorized e-mail address as defined in @recipients.

outer:
    foreach $send_to (split(/,/,$Config{'recipient'})) {

        # Eliminate any spurious leading or trailing whitespace in this
	# particular comma-separated subpart of the $Config{'recipient'}
	# CGI parameter value passed to the script.

        $send_to =~ s/^ //;
        $send_to =~ s/ $//;

	# Check that we still have a non-empty string.

	bad_recipient ("") unless ($send_to);

	# Syntax check the recipient address and fail if it is syntatically
	# invalid.

	if ($send_to =~ m/\@$/) {
	    bad_recipient ($send_to);
	} elsif ($send_to =~ m/^(.+)\@([^\@]+)$/) {
	    $cgi_userid = $1;
	    $cgi_domain = $2;
	    bad_recipient ($send_to) unless (valid_userid ($cgi_userid));
	    bad_recipient ($send_to) unless (valid_domain ($cgi_domain));
	} else {  # $send_to contains _zero_ at-signs
	    $cgi_userid = $send_to;
	    undef $cgi_domain;
	    bad_recipient ($send_to) unless (valid_userid ($cgi_userid));
	}

	# Check for matches with the @recipient_addresses array.

	# Note that we check the validity of the elements of the
	# @recipient_addresses array itself (as we go) here also.

	# Allow for both local addresses (like "joe") and potentially
	# non-local addresses (like "joe@example.com") here.

	# @domain portions, if present, are compared case-insensitively,
	# however user-ID portions are compared case-sensitively.

        foreach $recipient_address (@recipient_addresses) {
	    if ($recipient_address =~ m/\@$/) {
		error ('invalid_config_addr', $recipient_address);
	    } elsif ($recipient_address =~ m/^(.+)\@([^\@]+)$/) {
		$conf_userid = $1;
		$conf_domain = $2;
		error ('invalid_config_addr', $recipient_address)
		    if (!valid_userid ($conf_userid));
		error ('invalid_config_addr', $recipient_address)
		    if (!valid_domain ($conf_domain));
            } else { # $recipient_address contains _zero_ at-signs
		$conf_userid = $recipient_address;
		undef $conf_domain;
		error ('invalid_config_addr', $recipient_address)
		    if (!valid_userid ($conf_userid));
	    }

	    if (($cgi_userid eq $conf_userid)
		&& (defined $cgi_domain == defined $conf_domain)) {
		if (!defined $cgi_domain
		    || (defined $cgi_domain
			&& lc ($cgi_domain) eq lc ($conf_domain))) {
		    push @send_to, $send_to;
		    next outer;
		}
            }
        }

	# Check for matches with the @recipient_domains array.

	# Note that we check the validity of the elements of the
	# @recipient_domains array itself (as we go) here also.

	# Allow for both specific domain names (like "example.com") and
	# prefix wild-carded domains (like "*.example.com") here.

        foreach $recipient_domain (@recipient_domains) {
	    if ($recipient_domain =~ m/^\*\.(.+)$/) {
		$wild_carded = 1;
		$conf_domain = $1;
	    } else {
		$wild_carded = 0;
		$conf_domain = $recipient_domain;
	    }
	    error ('invalid_config_domain', $recipient_domain)
		if (!valid_domain ($conf_domain));

	    # Only for CGI parameter e-mail addresses that have a @domain
	    # suffix.  Check to see if the domain part matches something from
	    # the @recipient_domains array.  If the CGI parameter address
	    # doesn't have an @domain part, then skip these checks.  We can't
	    # match it against anything in @recipient_domains in that case.

	    if (defined $cgi_domain) {

		# When we are doing domain name matching, disallow ``tricky''
		# e-mail addresses where the user-ID parts contain percent
		# signs or exclamation points.  These could otherwise be
		# used to direct the e-mail output of this script to some
		# unintended destinations.

		next if ($cgi_userid =~ m/(\%|\!)/);

		if ($wild_carded) {
		    $dot_conf_domain = "." . $conf_domain;
		    $tail = substr ($cgi_domain, - length ($dot_conf_domain));
		    if (lc ($tail) eq lc ($dot_conf_domain)) {
			push @send_to, $send_to;
			next outer;
		    }
		} else {
		    if (lc ($cgi_domain) eq lc ($conf_domain)) {
			push @send_to, $send_to;
			next outer;
		    }
		}
	    }
        }
	# If we make it this far, then $send_to did not match anything
        bad_recipient ($send_to);
    }
    if ($#send_to < 0) {
	&error('no_recipient')
    }
    $Config{'recipient'} = join(',',@send_to);

    # Removed any poisoned nul bytes that may be present in the email CGI
    # parameter, just in case.  They might cause wacky behavior from some
    # mail servers.  See http://www.wiretrip.net/rfp/p/doc.asp/i2/d6.htm

    $Config{'email'} =~ tr /\0//d if ($Config{'email'});

    # Clean up the email CGI parameter further by replacing
    # all sequences of whitespace characters (including any possible
    # carriage returns and newlines) with a single space character.

    $Config{'email'} =~ s/\s+/ /gs if ($Config{'email'});

    # Removed any poisoned nul bytes that may be present in the realname CGI
    # parameter, just in case.  They might cause wacky behavior from some
    # mail servers.  See http://www.wiretrip.net/rfp/p/doc.asp/i2/d6.htm

    $Config{'realname'} =~ tr /\0//d if ($Config{'realname'});

    # Clean up the realname CGI parameter further by replacing
    # all sequences of whitespace characters (including any possible
    # carriage returns and newlines) with a single space character.

    $Config{'realname'} =~ s/\s+/ /gs if ($Config{'realname'});

    # Removed any poisoned nul bytes that may be present in the subject CGI
    # parameter, just in case.  They might cause wacky behavior from some
    # mail servers.  See http://www.wiretrip.net/rfp/p/doc.asp/i2/d6.htm

    $Config{'subject'} =~ tr /\0//d if ($Config{'subject'});

    # Clean up the subject CGI parameter further by replacing
    # all sequences of whitespace characters (including any possible
    # carriage returns and newlines) with a single space character.

    $Config{'subject'} =~ s/\s+/ /gs if ($Config{'subject'});

    # For each require field defined in the form:                            #
    foreach $require (@Required) {

        # If the required field is the email field, the syntax of the email  #
        # address if checked to make sure it passes a valid syntax.          #
        if ($require eq 'email' && !&valid_address($Config{$require})) {
            push(@error,$require);
        }

        # Otherwise, if the required field is a configuration field and it   #
        # has no value or has been filled in with a space, send an error.    #
        elsif (defined($Config{$require})) {
            if (!$Config{$require}) {
                push(@error,$require);
            }
        }

        # If it is a regular form field which has not been filled in or      #
        # filled in with a space, flag it as an error field.                 #
        elsif (!$Form{$require}) {
            push(@error,$require);
        }
    }

    # If any error fields have been found, send error message to the user.   #
    if (@error) { &error('missing_fields', @error) }
}

sub bad_recipient {
    my ($send_to) = @_;
    my $script_url;
    my $client_ip;
    my $orig_recipient_param;

    $script_url = "http://" . $ENV{'SERVER_NAME'} . ":" . 
                  $ENV{'SERVER_PORT'} . $ENV{'SCRIPT_NAME'};

    $client_ip = "[" . $ENV{'REMOTE_ADDR'} . "]";

    $orig_recipient_param = $Config{'recipient'};

    # Send a notice regarding possible script abuse to the $mail-admin

    open(MAIL,"|$mailprog -bm -f $mail_admin $mail_admin");

    print MAIL "To: $mail_admin\n";
    print MAIL "From: $script_url\n";
    print MAIL "Subject: Possible FormMail Script Abuse Detected\n";
    print MAIL "X-Generated-By: Matt Wright's FormMail.pl v1.9s-p7\n";
    print MAIL "X-Script-URL: $script_url\n";
    print MAIL "X-Originating-IP: $client_ip\n\n";

    print MAIL "A possible case of attempted FormMail script abuse has\n";
    print MAIL "been detected.  The attempt originated from $client_ip.\n";
    print MAIL "Additional details follow...\n\n";
    print MAIL "URL: $script_url\n";
    print MAIL "Recipient Parameter: $orig_recipient_param\n";

    close MAIL;

    error ('invalid_recipient', $send_to);
}

sub return_html {
    # Local variables used in this subroutine initialized.                   #
    local($key,$sort_order,$sorted_field);

    # If redirect option is used, print the redirectional location header.   #
    if ($Config{'redirect'}) {
	# Remove newlines, carriage returns and whitespace generally from
	# $Config{'redirect'}.  We don't want to allow clients to generate
	# their own added HTTP headers after all, now do we?
        $Config{'redirect'} =~ s/\s//g;
        print "Location: $Config{'redirect'}\n\n";
    }

    # Otherwise, begin printing the response page.                           #
    else {

        # Print HTTP header and opening HTML tags.                           #
        print "Content-type: text/html; charset=iso-8859-1\n\n";
        print "<html>\n <head>\n";

        # Print out title of page                                            #
        if ($Config{'title'}) { print "  <title>$Config{'title'}</title>\n" }
        else                  { print "  <title>Thank you for your request</title>\n"        }

        print " </head>\n <body";

        # Get Body Tag Attributes                                            #
        &body_attributes;

        # Close Body Tag                                                     #
        print ">\n  <center>\n";

        # Print custom or generic title.                                     #
        if ($Config{'title'}) { print "   <h1>$Config{'title'}</h1>\n" }
        else { print "   <h1>Thank you for submitting your request with Mountain States Lamb</h1>\n" }

        print "</center>\n";

        print "Below is the information you have provided to $Config{'recipient'} on ";
        print "$date<p><hr size=1 width=75\%><p>\n";

        # Sort alphabetically if specified:                                  #
        if ($Config{'sort'} eq 'alphabetic') {
            foreach $field (sort keys %Form) {

                # If the field has a value or the print blank fields option  #
                # is turned on, print out the form field and value.          #
                if ($Config{'print_blank_fields'} || $Form{$field}) {
                    print "<b>$field:</b> $Form{$field}<p>\n";
                }
            }
        }

        # If a sort order is specified, sort the form fields based on that.  #
        elsif ($Config{'sort'} =~ /^order:.*,.*/) {

            # Set the temporary $sort_order variable to the sorting order,   #
            # remove extraneous line breaks and spaces, remove the order:    #
            # directive and split the sort fields into an array.             #
            $sort_order = $Config{'sort'};
            $sort_order =~ s/(\s+|\n)?,(\s+|\n)?/,/g;
            $sort_order =~ s/(\s+)?\n+(\s+)?//g;
            $sort_order =~ s/order://;
            @sorted_fields = split(/,/, $sort_order);

            # For each sorted field, if it has a value or the print blank    #
            # fields option is turned on print the form field and value.     #
            foreach $sorted_field (@sorted_fields) {
                if ($Config{'print_blank_fields'} || $Form{$sorted_field}) {
                    print "<b>$sorted_field:</b> $Form{$sorted_field}<p>\n";
                }
            }
        }

        # Otherwise, default to the order in which the fields were sent.     #
        else {

            # For each form field, if it has a value or the print blank      #
            # fields option is turned on print the form field and value.     #
            foreach $field (@Field_Order) {
                if ($Config{'print_blank_fields'} || $Form{$field}) {
                    print "<b>$field:</b> $Form{$field}<p>\n";
                }
            }
        }

        print "<p><hr size=1 width=75%><p>\n";

        # Check for a Return Link and print one if found.                    #
        if ($Config{'return_link_url'} && $Config{'return_link_title'}) {
            print "<ul>\n";
            print "<li><a href=\"$Config{'return_link_url'}\">$Config{'return_link_title'}</a>\n";
            print "</ul>\n";
        }

        # Print the page footer.                                             #
        print <<"(END HTML FOOTER)";
        <hr size=1 width=75%><p> 
        <center><font size=-1><a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a>&copy; Matt Wright<br>
A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a></font></center>
        </body>
       </html>
(END HTML FOOTER)
    }
}

sub send_mail {
    # Localize variables used in this subroutine.                            #
    local($print_config,$key,$sort_order,$sorted_field,$env_report);

    my $script_url;
    my $client_ip;

    $script_url = "http://" . $ENV{'SERVER_NAME'} . ":" .
                  $ENV{'SERVER_PORT'} . $ENV{'SCRIPT_NAME'};

    $client_ip = "[" . $ENV{'REMOTE_ADDR'} . "]";

    # Open The Mail Program
    open(MAIL,"|$mailprog -bm -f $mail_admin -t 1>/dev/null");

    print MAIL "To: $Config{'recipient'}\n";
    print MAIL "From: $Config{'email'} ($Config{'Online Application'})\n";

    # Check for Message Subject
    if ($Config{'subject'}) { print MAIL "Subject: $Config{'subject'}\n" }
    else                    { print MAIL "Subject: Information from online request\n" }

    print MAIL "X-Generated-By: Matt Wright's FormMail.pl v1.9s-p7\n";
    print MAIL "X-Script-URL: $script_url\n";
    print MAIL "X-Originating-IP: $client_ip\n\n";

    print MAIL "Information from online request\n";
    print MAIL "$Config{'realname'} ($Config{'email'}) on $date\n";
    print MAIL "-" x 75 . "\n\n";

    if (@Print_Config) {
        foreach $print_config (@Print_Config) {
            if ($Config{$print_config}) {
                print MAIL "$print_config: $Config{$print_config}\n\n";
            }
        }
    }

    # Sort alphabetically if specified:                                      #
    if ($Config{'sort'} eq 'alphabetic') {
        foreach $field (sort keys %Form) {

            # If the field has a value or the print blank fields option      #
            # is turned on, print out the form field and value.              #
            if ($Config{'print_blank_fields'} || $Form{$field} ||
                $Form{$field} eq '0') {
                print MAIL "$field: $Form{$field}\n\n";
            }
        }
    }

    # If a sort order is specified, sort the form fields based on that.      #
    elsif ($Config{'sort'} =~ /^order:.*,.*/) {

        # Remove extraneous line breaks and spaces, remove the order:        #
        # directive and split the sort fields into an array.                 #
        $Config{'sort'} =~ s/(\s+|\n)?,(\s+|\n)?/,/g;
        $Config{'sort'} =~ s/(\s+)?\n+(\s+)?//g;
        $Config{'sort'} =~ s/order://;
        @sorted_fields = split(/,/, $Config{'sort'});

        # For each sorted field, if it has a value or the print blank        #
        # fields option is turned on print the form field and value.         #
        foreach $sorted_field (@sorted_fields) {
            if ($Config{'print_blank_fields'} || $Form{$sorted_field} ||
                $Form{$sorted_field} eq '0') {
                print MAIL "$sorted_field: $Form{$sorted_field}\n\n";
            }
        }
    }

    # Otherwise, default to the order in which the fields were sent.         #
    else {

        # For each form field, if it has a value or the print blank          #
        # fields option is turned on print the form field and value.         #
        foreach $field (@Field_Order) {
            if ($Config{'print_blank_fields'} || $Form{$field} ||
                $Form{$field} eq '0') {
                print MAIL "$field: $Form{$field}\n\n";
            }
        }
    }

    print MAIL "-" x 75 . "\n\n";

    # Send any specified Environment Variables to recipient.                 #
    foreach $env_report (@Env_Report) {
        if ($ENV{$env_report}) {
            print MAIL "$env_report: $ENV{$env_report}\n";
        }
    }

    close (MAIL);
}

sub valid_userid {
  my ($userid) = @_;

  # Disallow most special characters from the RFC 822/2822 reserved
  # character set in order to prevent tricky spammers from using
  # tricky e-mail address formats to trick this script into sending
  # e-mail to places where the installer did not intend to allow it
  # to go.

  return ($userid =~ m/[\(\)\[\]\{\}<>:;\@]/) ? 0 : 1;
}

sub valid_domain {
    my ($dom) = @_;

    if ($dom =~ m/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/) {
	return 1;
    } elsif ($dom =~ m/^([a-z0-9]+(?:-+[a-z0-9]+)*\.)+[a-z]+$/i) {
	return 1;
    } else {
	return 0;
    }
}

# Check that a full e-mail address of the form "user@domain" has valid
# overall syntax.

sub valid_address {
    # Initialize local email variable with input to subroutine.              #
    $email = $_[0];

    return ($email =~ m/^(.+)\@([^\@]+)$/
            && valid_userid ($1)
            && valid_domain ($2)) ? 1 : 0;
}

sub body_attributes {
    # Check for Background Color
    if ($Config{'bgcolor'}) { print " bgcolor=\"$Config{'bgcolor'}\"" }

    # Check for Background Image
    if ($Config{'background'}) { print " background=\"$Config{'background'}\"" }

    # Check for Link Color
    if ($Config{'link_color'}) { print " link=\"$Config{'link_color'}\"" }

    # Check for Visited Link Color
    if ($Config{'vlink_color'}) { print " vlink=\"$Config{'vlink_color'}\"" }

    # Check for Active Link Color
    if ($Config{'alink_color'}) { print " alink=\"$Config{'alink_color'}\"" }

    # Check for Body Text Color
    if ($Config{'text_color'}) { print " text=\"$Config{'text_color'}\"" }
}

sub error { 
    # Localize variables and assign subroutine input.                        #
    local($error,@error_fields) = @_;
    local($host,$missing_field,$missing_field_list);
    
    if ($error eq 'request_method') {
            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: Request Method</title>
 </head>
 <body bgcolor=#FFFFFF text=#000000>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: Request Method</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>The Request Method of the Form you submitted did not match
     either <tt>GET</tt> or <tt>POST</tt>.  Please check the form and make sure the
     <tt>method=</tt> statement is in upper case and matches <tt>GET</tt> or <tt>POST</tt>.<p>

     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
    }

    elsif ($error eq 'no_recipient') {
            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: No Recipient</title>
 </head>
 <body bgcolor=#FFFFFF text=#000000>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: No Recipient</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>There was no recipient address specified in the data sent to FormMail.  Please
     make sure you have filled in the <tt>recipient</tt> form field with an e-mail
     address that has been configured in
     either <tt>\@recipient_addresses</tt>
     or in <tt>\@recipient_domains</tt>.
     More information on filling in <tt>recipient</tt> form fields
     and variables can be
     found in the README file.<hr size=1>

     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
    }

    elsif ($error eq 'invalid_recipient') {
            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: Invalid Recipient</title>
 </head>
 <body bgcolor=#FFFFFF text=#000000>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: Invalid Recipient</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>There was an invalid recipient address specified in the data sent to FormMail.  Please
     make sure you have filled in the <tt>recipient</tt> form field with an e-mail
     address that has been configured in
     either <tt>\@recipient_addressess</tt>
     or else in <tt>\@recipient_domains</tt>.
     More information on filling in <tt>recipient</tt> form fields and
     variables can be
     found in the README file.<hr size=1>

     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
    }

    elsif ($error eq 'invalid_config_addr') {
            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: Invalid Configured Recipient Address</title>
 </head>
 <body bgcolor=#FFFFFF text=#000000>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: Invalid Configured Recipient Address</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>There was an invalid recipient address specified in the
     recipient_addresses configuration variable of FormMail.  Please
     contact the FormMail installer and report this error.
     More information on installing and configuring FormMail can be
     found in the README file.<hr size=1>

     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
    }

    elsif ($error eq 'invalid_config_domain') {
            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: Invalid Configured Recipient Domain</title>
 </head>
 <body bgcolor=#FFFFFF text=#000000>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: Invalid Configured Recipient Domain</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>There was an invalid recipient domain specified in the
     recipient_domains configuration variable of FormMail.  Please
     contact the FormMail installer and report this error.
     More information on installing and configuring FormMail can be
     found in the README file.<hr size=1>

     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
    }

    elsif ($error eq 'missing_fields') {
        if ($Config{'missing_fields_redirect'}) {
            print "Location: $Config{'missing_fields_redirect'}\n\n";
        }
        else {
            foreach $missing_field (@error_fields) {
                $missing_field_list .= "      <li>$missing_field\n";
            }

            print <<"(END ERROR HTML)";
Content-type: text/html; charset=iso-8859-1

<html>
 <head>
  <title>Error: Blank Fields</title>
 </head>
  <center>
   <table border=0 width=600 bgcolor=#9C9C9C>
    <tr><th><font size=+2>Error: Blank Fields</font></th></tr>
   </table>
   <table border=0 width=600 bgcolor=#CFCFCF>
    <tr><td>The following fields were left blank in your submission form:<p>
     <ul>
$missing_field_list
     </ul><br>

     These fields must be filled in before you can successfully submit the form.<p>
     Please use your browser's back button to return to the form and try again.<hr size=1>
     <center><font size=-1>
      <a href="http://www.worldwidemart.com/scripts/formmail.shtml">FormMail</a> &copy; Matt Wright<br>
      A Free Product of <a href="http://www.worldwidemart.com/scripts/">Matt's Script Archive, Inc.</a>
     </font></center>
    </td></tr>
   </table>
  </center>
 </body>
</html>
(END ERROR HTML)
        }
    }

    exit (0);
}

