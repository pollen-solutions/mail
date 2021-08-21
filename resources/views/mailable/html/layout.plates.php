<?php
/**
 * @var Pollen\Mail\MailableTemplateInterface $this
 * @see https://tedgoas.github.io/Cerberus/
 */
?>
<!DOCTYPE html>
<html lang="<?php echo $this->getLocale(); ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="<?php echo $this->getCharset(); ?>">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title><?php echo $this->getSubject(); ?></title>

    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

    <?php if ($css = $this->getCssProperties()) : ?>
        <style><?php echo $css; ?></style>
    <?php endif; ?>
</head>

<body width="100%">
<center class="center" role="article" aria-roledescription="email" lang="fr">
    <!--[if mso | IE]>
    <table class="msoTableWrapper" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
    <![endif]-->
                <div class="EmailContainer email-container">
                    <!--[if mso]>
                    <table class="msoTableEmailContainer" align="center" role="presentation" cellspacing="0" cellpadding="0"
                           border="0" width="680">
                        <tr>
                            <td>
                    <![endif]-->
                    <table class="tableEmailHeader" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <?php echo $this->section('header'); ?>
                    </table>

                    <table class="tableEmailBody" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <?php echo $this->section('content'); ?>
                    </table>

                    <table class="tableEmailFooter" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <?php echo $this->section('footer'); ?>
                    </table>
                    <!--[if mso]>
                            </td>
                        </tr>
                    </table>
                    <![endif]-->
                </div>
    <!--[if mso | IE]>
            </td>
        </tr>
    </table>
    <![endif]-->
</center>
</body>
</html>