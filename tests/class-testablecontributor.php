<?php
/**
 * File for testing contributors functionality.
 *
 * @created 2025-04-13
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp/tests
 */

/**
 * Overriding actual contributor class for unit testing with limited constraint.
 */
class TestableContributor extends \rtCamp\Contributor {

	/**
	 * Overriding for unit testing.
	 */
	protected function should_display_contributors() {
		return true; // Always display in tests.
	}
}
