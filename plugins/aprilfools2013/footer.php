<?php
if (!isset($_COOKIE['donations']) && date('m-d') === '04-01')
$footerExtensionsB .= <<<HTML
<div style="top: 0; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); position: fixed" id=donations>
  <div style="background: #EEE; color: black; font-size: 16px; width: 600px; margin: 20px auto; text-align: justify; padding: 10px; border-radius: 4px;">
    Starting today, the ABXD is paid software. The software and support
    isn't available anymore without payment. We were forced to do that,
    in order to improve quality of ABXD.<br><br>

    Previously, we (developers) were working on ABXD in our free time
    paying for servers (such as this ABXD Development Server) from our
    pockets. But that isn't possible anymore. The server on which the
    support board is hosted, costs $20 monthly. That's lots of cash,
    but trust us, it's one of cheapest 3GB of RAM options.<br><br>

    The cash from donations also would let do other things, like an
    iOS application for ABXD. We all know that Apple certification
    program is expensive, but some of you (1 person) wanted an
    application for ABXD. We need $100 yearly in order to develop
    for iOS.

    We are aware that not everybody has lots of cash. We ask you to
    donate some money. Even $0.01 can be useful, but we would like to
    have more.<br><br>

    <marquee><a href="javascript:open('https://cms.paypal.com/c2/cgi-bin/?cmd=_render-content&content_ID=marketing_c2/abxd'); alert('Thank you for donating.'); $('#donations').remove(); document.cookie = 'donations=1; expires=Tue, 02 Apr 2013 12:00:00 GMT'; void 0">
      <center><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate" width="500" height="100"></center></a></marquee>
  </div>
</div>
HTML;
