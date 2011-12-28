<?php defined('_JEXEC') or die('Restricted access'); 

JHTML::_('behavior.tooltip');
JHTML::_('behavior.mootools');

$document=JFactory::getDocument();
$document->addStyleSheet('components/com_get_soft/assets/css/gallery.css');
$document->addScript('components/com_get_soft/assets/js/gallery.js');

$config=$this->config;
$pageNav=$this->pageNav;
$images=$this->images;

?>
<h2>Theme #1</h2>
<div class="gallery">
<?php
		for ($i=0;$i<count($images);$i++)
		{			
			$obj_image=$images[$i];			
			?>
				<div class="img_ad_box">
					<div class="img_box">
						<a class="img_small_thumb" title="<?php echo  htmlspecialchars($obj_image->get('name')); ?>" href="<?php echo $obj_image->get_image_page_link(); ?>">
							<img src="<?php echo $obj_image->get_small_thumb('link'); ?>" alt="<?php echo  htmlspecialchars($obj_image->get('name')); ?>" />	
						</a>				
					</div>
					<div class="img_name"><?php echo  $obj_image->get('name'); ?></div>
					<?php if($config->allow_report) {?>
					<div class="img_report" style="display: none;"> 
						<a class="" href="<?php echo  $obj_image->get_report_link(); ?>" onclick="return confirm('<?php echo  JText::_('Are you sure?') ?>');">
							<?php echo  JText::_('Report')?>	
						</a>
					</div>
					<?php } ?>
				</div>
			<?php
		}
		?>
		<div style="clear:both"></div>
		<br />
		<?php
		echo $pageNav->getPagesLinks();
?>
</div>