<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jsend
 *
 * @copyright   Copyright (C) 2012 Saity 74 LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

$list = trim($params->get('list', ''));
$list_arr = explode("\n", $list);

if ($list && !empty($list_arr)){
    $list = array();
    foreach($list_arr as $option){
        $list[] = JHTML::_('select.option', $option , $option );
    }
    $list_html = JHTML::_('select.genericlist', $list, $name = 'list', 'inputbox');
}

$uri = JUri::getInstance()->toString(array('path'));

if(!function_exists("is_required")) {
	function is_required($val) {
		return $val == 2;
	};
}

$fields = [
	'name' => array('type' => 'text'),
	'phone' => array('type' => 'text', 'pattern' => '^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$'),
	'email' => array('type' => 'email'),
	'firm' => array('type' => 'text')
];

$form_id = 'jForm'.$module->id.$module->position;
$form_sfx = $params->get('form_suffix', '');
$form_class = 'm-form';
if ($form_sfx) {
	$form_sfx = 'm-form-'.$form_sfx;
	$form_class .= ' '.$form_sfx;
}
if ($params->get('show_in_modal')) {
	$form_class .= '-in-modal';
}


$header = $params->get('header', '');
if ($header) {
	$header_tag = $params->get('header_tag', 'h3');
	$header_class = 'm-form_heading_title';
	if($form_sfx){
		$header_class .= ' '.$form_sfx.'_heading_title';
	}
	$header = '<'.$header_tag.' class="'.$header_class.'">'.$header.'</'.$header_tag.'>';
}
$caption = $params->get('caption', '');
?>

<?php if ($params->get('show_in_modal')): ?>
	<a href="#<?php echo 'modal'.$form_id; ?>" class="js-modal-show <?php echo $params->get('modal_btnclass', 'e-btn') ?>" data-form="true">
		<?php echo $params->get('modal_btntext'); ?>
	</a>
<?php endif; ?>

<?php if ($params->get('show_in_modal')): ?>
	<div id="<?php echo 'modal'.$form_id ?>" class="m-modal -with-form mfp-hide">
<?php endif; ?>

<div id="<?php echo $form_id; ?>" class="<?php echo $form_class; ?>">
	<form class="js-form" method="POST"
		  action="<?php echo $uri; ?>"
		  enctype="multipart/form-data"
		  data-tmpl="send"
		<?php if($params->get('ajax_send')) echo 'data-ajax="true"'; ?>>

		<?php if ($header || $caption): ?>
			<div class="m-form_heading <?php if($form_sfx) echo $form_sfx.'_heading'; ?>">
				<?php if ($header) echo $header; ?>
				<?php if ($caption): ?>
				<div class="m-form_heading_caption <?php if($form_sfx) echo $form_sfx.'_heading_caption'; ?>">
					<?php echo $caption; ?>
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12">
				<?php foreach ($fields as $field => $options): ?>
				<?php if ($show = $params->get('show'.$field, 0)) : ?>
					<div class="m-form_box <?php if($form_sfx) echo $form_sfx.'_box' ?>">
						<input type="<?php echo $options['type']; ?>"
							class="m-form_input <?php if($form_sfx) echo $form_sfx.'_input'; ?>"
							value="" name="<?php echo $field; ?>"
							placeholder="<?php echo $params->get($field.'_placeholder'); ?>"
							<?php if(is_required($show)) echo "required"; ?>
							<?php if(isset($options['pattern'])) echo 'pattern="'.$options['pattern'].'"'; ?>/>
					</div>
				<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12">
				<?php if ($showcomment = $params->get('showcomment', 0)) : ?>
					<div class="m-form_box <?php if($form_sfx) echo $form_sfx.'_box'; ?>">
						<textarea class="m-form_textarea <?php if($form_sfx) echo $form_sfx.'_textarea'; ?>"
								  placeholder="<?php echo $params->get('comment_placeholder'); ?>"
								  name="comment" rows="5"
								<?php if(is_required($show)) echo "required"; ?>></textarea>
					</div>
				<?php endif; ?>

				 <?php if ($showlist = $params->get('showlist', 0)) : ?>
					<div class="m-form_box <?php if($form_sfx) echo $form_sfx.'_box'; ?>">
						<div class="m-form_selectbox <?php if($form_sfx) echo $form_sfx.'_selectbox'; ?>" <?php if(is_required($showlist)) echo "required"; ?>>
							<?php echo $list_html; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-12">
				<div class="row">
					<?php if ($showattachment = $params->get('showattachment', 0)) : ?>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 col-xs-push-0 col-sm-push-6 col-md-push-6 col-lg-push-0">
						<div class="m-form_box <?php if($form_sfx) echo $form_sfx.'_box'; ?>">
							<div class="m-form_attachment js-attachment <?php if($form_sfx) echo $form_sfx.'_attachment'; ?>">
								<div class="m-form_attachment_box <?php if($form_sfx) echo $form_sfx.'_attachment_box' ?>">
									<div class="m-form_attachment_btn <?php if($form_sfx) echo $form_sfx.'_attachment_btn'; ?>">
										<?php echo $params->get('attachment_caption'); ?>
									</div>
									<input class="m-form_attachment_input js-attachment-input <?php if($form_sfx) echo $form_sfx.'_attachment_input' ?>" name="Filedata" type="file"/>
								</div>
								<div class="m-form_attachment_filename <?php if($form_sfx) echo $form_sfx.'_attachment_filename'; ?>">
									<span class="js-attachment-filename"></span>
									<i class="js-attachment-remove i-icon i-cross"></i>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-12 col-xs-pull-0 col-sm-pull-6 col-md-pull-6 col-lg-pull-0">
						<div class="m-form_box m-form_submit <?php if($form_sfx) echo $form_sfx.'_box '.$form_sfx.'_submit'; ?>">
							<button class="m-form_submit_btn e-btn <?php if($form_sfx) echo $form_sfx.'_submit_btn'; ?> <?php echo $params->get('btnclass'); ?>" type="submit">
								<?php echo $params->get('btntext'); ?>
							</button>
							<div class="e-loader"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="hash" value="<?php echo base64_encode($module->position) ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>

<?php if ($params->get('show_in_modal')): ?>
	</div>
<?php endif; ?>
