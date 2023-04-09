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
	<Field v-bind.sync="$attrs"
		:title="title"
		:edit.sync="edit"
		:allow-title-edit="allowTitleEdit"
		:allow-deletion="allowDeletion"
		:enable-drag-handle="enableDragHandle"
		:title-placeholder="t('biblio', 'Title')"
		@update:title="onTitleChange"
		@delete="onDelete">
		<div class="field__content">
			<input ref="input"
				:placeholder="t('biblio', 'Value')"
				:disabled="!allowValueEdit"
				:value="value"
				class="field__input"
				minlength="1"
				type="text"
				@input="onInput"
				@keydown.enter.exact.prevent="onKeydownEnter">
		</div>
	</Field>
</template>

<script>
import FieldMixin from "../../mixins/FieldMixin";

export default {
	name: "ShortTextField",

	mixins: [FieldMixin],

	methods: {
		onInput() {
			const input = this.$refs.input;
			this.$emit("update:value", input.value);
		},
	},
};
</script>

<style lang="scss" scoped>
// Using type to have a higher order than the input styling of server
.field__input[type=text] {
	width: 100%;
	min-height: 44px;
	margin: 0;
	padding: 6px 0;
	border: 0;
	border-bottom: 1px dotted var(--color-border-dark);
	border-radius: 0;

	&:disabled {
		// Just overrides Server CSS-Styling for disabled inputs. -> Not Good??
		background-color: var(--color-main-background);
		color: var(--color-main-text);
	}
}

</style>
