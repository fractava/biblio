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
			<NcInputField ref="input"
				:placeholder="t('biblio', 'Value')"
				:disabled="!allowValueEdit"
				:value="value"
				minlength="0"
				type="text"
				@update:value="onInput"
				@keydown.enter.exact.prevent="onKeydownEnter" />
		</div>
	</Field>
</template>

<script>
import FieldMixin from "../../mixins/FieldMixin";

import NcInputField from '@nextcloud/vue/dist/Components/NcInputField.js'

export default {
	name: "ShortTextField",

	components: {
		NcInputField,
	},

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

</style>
