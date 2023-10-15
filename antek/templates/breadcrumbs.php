<?php
if ( $breadcrumb_items = ideapark_breadcrumb_list() ) { ?>
	<nav class="c-breadcrumbs">
		<ol class="c-breadcrumbs__list" itemscope itemtype="http://schema.org/BreadcrumbList">
			<?php
			$i        = 1;
			foreach ( $breadcrumb_items as $item_index => $item ):
				$title = isset( $item['title'] ) ? $item['title'] : '';
				$link = isset( $item['link'] ) ? $item['link'] : '';
				?>
				<li class="c-breadcrumbs__item <?php ideapark_class( ! $item_index, 'c-breadcrumbs__item--first' ); ?> <?php ideapark_class( $item_index == sizeof( $breadcrumb_items ) - 1, 'c-breadcrumbs__item--last' ); ?>"
					itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a itemprop="item" title="<?php echo esc_attr( $title ); ?>" href="<?php echo esc_url( $link ); ?>"><span
							itemprop="name"><?php echo esc_html( $title ); ?></span></a>
					<meta itemprop="position" content="<?php echo esc_attr( $i ); ?>">
				</li>
				<?php
				$i ++;
			endforeach;
			?>
		</ol>
	</nav>
<?php } ?>