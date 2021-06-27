/**
 * @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
import { debounce } from 'debounce'
import { showError } from '@nextcloud/dialogs'

import Field from '../components/Fields/Field'

export default {
	inheritAttrs: false,
	props: {

		/**
		 * The Field title
		 */
		title: {
			type: String,
			required: true,
		},

		/**
		 * Required-Setting
		 */
		isRequired: {
			type: Boolean,
			required: true,
		},

		/**
		 * The fields value
		 */
		value: {
		},

		/**
		 * Field type model object
		 */
		fieldType: {
			type: Object,
			required: true,
		},

		/**
		 * Submission or Edit-Mode
		 */
		readOnly: {
			type: Boolean,
			default: false,
		},
	},

	components: {
		Field,
	},

	data() {
		return {
			// Do we display this Field in edit or fill mode
			edit: true,
		}
	},

	methods: {
		/**
		 * Forward the title change to the parent and store to db
		 *
		 * @param {string} text the title
		 */
		onTitleChange: debounce(function(text) {
			this.$emit('update:title', text)
		}, 200),

		/**
		 * Forward the value change to the parent
		 *
		 * @param {Array} value the array of entries
		 */
		onValueChange(value) {
			this.$emit('update:value', value)
		},

		/**
		 * Delete this Field
		 */
		onDelete() {
			this.$emit('delete')
		},

		/**
		 * Don't automatically submit form on Enter, parent will handle that
		 * To be called with prevent: @keydown.enter.prevent="onKeydownEnter"
		 * @param {Object} event The fired event
		 */
		onKeydownEnter(event) {
			this.$emit('keydown', event)
		},

		/**
		 * Focus the first focusable element
		 */
		focus() {
			this.edit = true
			this.$el.scrollIntoView({ behavior: 'smooth' })
			this.$nextTick(() => {
				const title = this.$el.querySelector('.field__header-title')
				if (title) {
					title.select()
				}
			})
		},
	},
}
