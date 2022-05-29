<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo $this->get_title(); ?></title>
	<style type="text/css"><?php $this->template_styles(); ?></style>
	<style type="text/css">
        @font-face {
            font-family: 'CooperBlackRegular';
            font-style: normal;
            font-weight: bold;
            src: local('CooperBlackRegular Bold'), local('CooperBlackRegular-Bold'), url(<?php echo $this->get_template_path(); ?>/fonts/Cooper-Black-Regular.ttf) format('truetype');
        }
        <?php do_action( 'wpo_wcpdf_custom_styles', $this->get_type(), $this ); ?>
    </style>
</head>
<body class="<?php echo $this->get_type(); ?>">
<?php echo $content; ?>
</body>
</html>