<!--
  - @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @author John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<Field
		v-bind.sync="$attrs"
		:title="title"
		:edit.sync="edit"
		:allow-title-edit="allowTitleEdit"
		:allow-deletion="allowDeletion"
		:enable-drag-handle="enableDragHandle"
		@update:title="onTitleChange"
		@delete="onDelete">
		<div class="field__content">
			<textarea ref="textarea"
				:placeholder="fieldType.valuePlaceholder"
				:disabled="!allowValueEdit"
				:value="value"
				class="field__text"
				minlength="1"
				@input="onInput"
				@keypress="autoSizeText"
				@keydown.ctrl.enter="onKeydownCtrlEnter" />
		</div>
	</Field>
</template>

<script>
import FieldMixin from '../../mixins/FieldMixin'

export default {
	name: 'LongTextField',

	mixins: [FieldMixin],

	data() {
		return {
			height: 1,
		}
	},

	mounted() {
		this.autoSizeText()
	},

	methods: {
		onInput() {
			const textarea = this.$refs.textarea
			this.$emit('update:value', textarea.value)
			this.autoSizeText()
		},
		autoSizeText() {
			const textarea = this.$refs.textarea
			textarea.style.cssText = 'height:auto; padding:0'
			textarea.style.cssText = `height: ${textarea.scrollHeight + 20}px`
		},
		onKeydownCtrlEnter(event) {
			this.$emit('keydown', event)
		},
	},
}
</script>

<style lang="scss" scoped>
.field__text {
	// make sure height calculations are correct
	box-sizing: content-box !important;
	width: 100%;
	min-width: 100%;
	max-width: 100%;
	min-height: 44px;
	margin: 0;
	padding: 6px 0;
	border: 0;
	border-bottom: 1px dotted var(--color-border-dark);
	border-radius: 0;
	resize: none;
	font-size: 14px;

	&:disabled {
		// Just overrides Server CSS-Styling for disabled inputs. -> Not Good??
		background-color: var(--color-main-background);
		color: var(--color-main-text);
	}
}

</style>
