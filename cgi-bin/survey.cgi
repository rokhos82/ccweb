#!/usr/bin/perl

use CGI::Carp qw(fatalsToBrowser);

#---------------------------------------------------------------------
# Retrieve Values from Form
#---------------------------------------------------------------------

read(STDIN, $crazystring, $ENV{'CONTENT_LENGTH'});
@pairs = split(/&/, $crazystring);

foreach $pair (@pairs) {
    ($name, $value) = split(/=/, $pair);
    $value =~ tr/+/ /;
    $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
	if ($FORM{$name} ne ""){
    	$FORM{$name} = $FORM{$name}.", ".$value;
	}
	else {
		$FORM{$name} = $value;
	}
}


#---------------------------------------------------------------------
# Write E-Mail
#---------------------------------------------------------------------

$mailprog = '/bin/sendmail';
$recipient = $FORM{"recipient"};
open (MAIL, "|$mailprog -t") or &dienice("Can't access $mailprog!\n");

print MAIL "To: $recipient\n";
print MAIL "Reply-to: \n";
print MAIL "Subject: ConverseCounty.org Form Reply\n";
print MAIL "Content-type:text/html\n\n";

$whichsurvey = $FORM{"whichsurvey"};
$whichsurvey .= ".file";

open (SURVEY_INF, $whichsurvey) or &dienice("Can't access " . $whichsurvey);
seek (SURVEY_INF, 0, 2);
$howlong = tell(SURVEY_INF);
seek (SURVEY_INF, 0, 0);
read (SURVEY_INF, $survey_inf, $howlong);

foreach $key (keys(%FORM)) {
    $survey_inf =~ s/\<\!\-\-$key\-\-\>/$FORM{$key}/
}

print MAIL $survey_inf;

close(MAIL);

#---------------------------------------------------------------------
# Write Thank You Message to Browser
#---------------------------------------------------------------------

print 'Status: 302 Moved', "\r\n", 'Location: http://www.conversecounty.org/other/contact_us.thank_you.htm', "\r\n\r\n";

#---------------------------------------------------------------------
# Subroutine Name: dienice
# Description: Dies Nicely with an error message :)
#---------------------------------------------------------------------

sub dienice {
    ($errmsg) = @_;
    print "<h2>Error</h2>\n";
    print "$errmsg<p>\n";
    exit;
}