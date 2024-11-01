<?php
/**
 * Ninety Meeting Calendar widget class
 * Heavily based on
 * https://github.com/thingsym/custom-post-type-widgets/blob/master/inc/widget-custom-post-type-calendar.php
 *
 * @since   0.1.0
 * @package Ninety in Ninety
 */

class Ninety_Meeting_Calendar extends WP_Widget {

	/**
	 * Ensure that the ID attribute only appears in the markup once
	 *
	 * @since  0.1.0
	 * @static
	 * @access private
	 * @var int
	 */
	private static $instance = 0;

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_calendar',
			'description' => __( 'A calendar of your Meetings.', 'ninety-ninety' ),
		);
		parent::__construct( 'ninety-meeting-calendar', __( 'Calendar ( Meetings )', 'ninety-ninety' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Calendar', 'ninety-ninety' ) : $instance['title'], $instance, $this->id_base );

		add_filter( 'get_calendar', array( $this, 'get_meeting_calendar' ), 10, 3 );
		add_filter( 'month_link', array( $this, 'get_month_link_meeting' ), 10, 3 );
		add_filter( 'day_link', array( $this, 'get_day_link_meeting' ), 10, 4 );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div id="calendar_wrap" class="calendar_wrap">';
		} else {
			echo '<div class="calendar_wrap">';
		}

		get_calendar();

		echo '</div>';
		echo $args['after_widget'];

		remove_filter( 'month_link', array( $this, 'get_month_link_meeting' ) );
		remove_filter( 'day_link', array( $this, 'get_day_link_meeting' ) );

		self::$instance ++;
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

//		$instance['posttype'] = strip_tags( $new_instance['posttype'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ninety-ninety' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
				   value="<?php echo esc_attr( $title ); ?>"/></p>

		<?php
	}

	/**
	 * Extend the get_calendar
	 *
	 * @param string  $calendar_output
	 * @param boolean $initial
	 * @param boolean $echo
	 *
	 * @since 0.1.0
	 * @see   wp-includes/general-template.php
	 */
	public function get_meeting_calendar( $calendar_output, $initial = true, $echo = true ) {
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

		$posttype = 'ninety_meeting';

		$key   = md5( $posttype . $m . $monthnum . $year );
		$cache = wp_cache_get( 'get_calendar', 'calendar' );

		// Remove filter before applying it below to prevent max function nesting level error.
		// Do this outside the if statement because it needs to be removed regardless.
		remove_filter( 'get_calendar', array( $this, 'get_meeting_calendar' ) );

		if ( $cache && is_array( $cache ) && isset( $cache[ $key ] ) ) {
			/** This filter is documented in wp-includes/general-template.php */
			$output = apply_filters( 'get_calendar', $cache[ $key ] );
			if ( $echo ) {
				echo $output;

				return;
			}

			return $output;
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		// Quick check. If we have no posts at all, abort!
		if ( ! $posts ) {
			$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = '$posttype' AND post_status = 'publish' LIMIT 1" );
			if ( ! $gotsome ) {
				$cache[ $key ] = '';
				wp_cache_set( 'get_calendar', $cache, 'calendar' );

				return;
			}
		}

		if ( isset( $_GET['w'] ) ) {
			$w = (int) $_GET['w'];
		}

		// week_begins = 0 stands for Sunday.
		$week_begins = (int) get_option( 'start_of_week' );
		$ts          = current_time( 'timestamp' );

		// Let's figure out when we are.
		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$thismonth = zeroise( intval( $monthnum ), 2 );
			$thisyear  = (int) $year;
		} elseif ( ! empty( $w ) ) {
			// We need to get the month from MySQL.
			$thisyear = (int) substr( $m, 0, 4 );
			// it seems MySQL's weeks disagree with PHP's.
			$d = ( ( $w - 1 ) * 7 ) + 6;

			$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')" );
		} elseif ( ! empty( $m ) ) {
			$thisyear = (int) substr( $m, 0, 4 );
			if ( strlen( $m ) < 6 ) {
				$thismonth = '01';
			} else {
				$thismonth = zeroise( (int) substr( $m, 4, 2 ), 2 );
			}
		} else {
			$thisyear  = gmdate( 'Y', $ts );
			$thismonth = gmdate( 'm', $ts );
		}

		$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );
		$last_day  = date( 't', $unixmonth );

		// Get the next and previous month and year with at least one post
		$previous = $wpdb->get_row( "SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date < '$thisyear-$thismonth-01'
			AND post_type = '$posttype' AND post_status = 'publish'
				ORDER BY post_date DESC
				LIMIT 1" );
		$next     = $wpdb->get_row( "SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
			AND post_type = '$posttype' AND post_status = 'publish'
				ORDER BY post_date ASC
				LIMIT 1" );

		/* translators: Calendar caption: 1: month name, 2: 4-digit year */
		$calendar_caption = _x( '%1$s %2$s', 'calendar caption' );
		$calendar_output  = '<table id="wp-calendar">
		<caption>' . sprintf(
				$calendar_caption,
				$wp_locale->get_month( $thismonth ),
				date( 'Y', $unixmonth )
			) . '</caption>
		<thead>
		<tr>';

		$myweek = array();

		for ( $wdcount = 0; $wdcount <= 6; $wdcount ++ ) {
			$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		}

		foreach ( $myweek as $wd ) {
			$day_name        = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
			$wd              = esc_attr( $wd );
			$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
		}

		$calendar_output .= '
		</tr>
		</thead>

		<tfoot>
		<tr>';

		if ( $previous ) {
			$calendar_output .= "\n\t\t" . '<td colspan="3" id="prev"><a href="' . get_month_link( $previous->year, $previous->month ) . '">&laquo; ' .
			                    $wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) .
			                    '</a></td>';
		} else {
			$calendar_output .= "\n\t\t" . '<td colspan="3" id="prev" class="pad">&nbsp;</td>';
		}

		$calendar_output .= "\n\t\t" . '<td class="pad">&nbsp;</td>';

		if ( $next ) {
			$calendar_output .= "\n\t\t" . '<td colspan="3" id="next"><a href="' . get_month_link( $next->year, $next->month ) . '">' .
			                    $wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) .
			                    ' &raquo;</a></td>';
		} else {
			$calendar_output .= "\n\t\t" . '<td colspan="3" id="next" class="pad">&nbsp;</td>';
		}

		$calendar_output .= '
		</tr>
		</tfoot>

		<tbody>
		<tr>';

		$daywithpost = array();

		// Get days with posts
		$dayswithposts = $wpdb->get_results( "SELECT DAYOFMONTH(post_date) AS meeting_date, COUNT(post_title) as meeting_count
			FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
			AND post_type = '$posttype' AND post_status = 'publish'
			AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' GROUP BY meeting_date ORDER BY meeting_date", ARRAY_N );

		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[ $daywith[0] ] = $daywith[1];
			}
		}

		// See how much we should pad in the beginning
		$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
		if ( 0 != $pad ) {
			$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
		}

		$newrow      = false;
		$daysinmonth = (int) date( 't', $unixmonth );

		for ( $day = 1; $day <= $daysinmonth; ++ $day ) {
			if ( isset( $newrow ) && $newrow ) {
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			}
			$newrow = false;

			if ( current_time( 'j' ) == $day &&
			     current_time( 'm' ) == $thismonth &&
			     current_time( 'Y' ) == $thisyear ) {
				$calendar_output .= '<td id="today">';
			} else {
				$calendar_output .= '<td>';
			}

			if ( isset( $daywithpost[ $day ] ) ) {
				// any posts today?
				$date_format = date( _x( 'F j, Y', 'daily archives date format' ), strtotime( "{$thisyear}-{$thismonth}-{$day}" ) );
				// translators: when the meetings happened.
				$label           = sprintf( __( 'Meetings took place on %s' ), $date_format );
				$calendar_output .= sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					get_day_link( $thisyear, $thismonth, $day ),
					// $this->get_custom_post_type_day_link( $posttype, $thisyear, $thismonth, $day ),
					esc_attr( $label ),
					$this->maybe_bold_day( $day, $daywithpost[ $day ] )
				);
			} else {
				$calendar_output .= $day;
			}
			$calendar_output .= '</td>';

			if ( 6 == calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
				$newrow = true;
			}
		}

		$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
		if ( 0 != $pad && 7 != $pad ) {
			$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
		}

		$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

		$cache[ $key ] = $calendar_output;
		wp_cache_set( 'get_calendar', $cache, 'calendar' );

		if ( $echo ) {
			echo $calendar_output;
		} else {
			return $calendar_output;
		}
	}

	private function maybe_bold_day( $day, $num_meetings ) {
		$day = $num_meetings > 1 ? '<strong class="ninety-multiple-meetings">' . $day . '</strong>' : strval( $day );

		return $day;
	}

	public function get_day_link_meeting( $daylink, $year, $month, $day ) {
		global $wp_rewrite;

		$posttype = 'ninety_meeting';

		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}
		if ( ! $day ) {
			$day = gmdate( 'j', current_time( 'timestamp' ) );
		}

		$daylink = $wp_rewrite->get_day_permastruct();

		if ( ! empty( $daylink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$daylink = str_replace( '%year%', $year, $daylink );
			$daylink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $daylink );
			$daylink = str_replace( '%day%', zeroise( intval( $day ), 2 ), $daylink );

			if ( 'post' == $posttype ) {
				$daylink = home_url( user_trailingslashit( $daylink, 'day' ) );
			} else {
				$type_obj     = get_post_type_object( $posttype );
				$archive_name = ! empty( $type_obj->rewrite['slug'] ) ? $type_obj->rewrite['slug'] : $posttype;
				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$daylink   = str_replace( $front, $new_front . '/' . $archive_name, $daylink );
					$daylink   = home_url( user_trailingslashit( $daylink, 'day' ) );
				} else {
					$daylink = home_url( user_trailingslashit( $archive_name . $daylink, 'day' ) );
				}
			}
		} else {
			$daylink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) );
		}

		return $daylink;
	}

	public function get_month_link_meeting( $monthlink, $year, $month ) {
		global $wp_rewrite;

		$posttype = 'ninety_meeting';

		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}

		$monthlink = $wp_rewrite->get_month_permastruct();

		if ( ! empty( $monthlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$monthlink = str_replace( '%year%', $year, $monthlink );
			$monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $monthlink );

			if ( 'post' == $posttype ) {
				$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
			} else {
				$type_obj     = get_post_type_object( $posttype );
				$archive_name = ! empty( $type_obj->rewrite['slug'] ) ? $type_obj->rewrite['slug'] : $posttype;
				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$monthlink = str_replace( $front, $new_front . '/' . $archive_name, $monthlink );
					$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
				} else {
					$monthlink = home_url( user_trailingslashit( $archive_name . $monthlink, 'month' ) );
				}
			}
		} else {
			$monthlink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) );
		}

		return $monthlink;
	}

}
