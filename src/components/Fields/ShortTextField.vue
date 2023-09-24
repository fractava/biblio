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
		:name="name"
		:edit="true"
		:allow-name-edit="allowNameEdit"
		:allow-deletion="allowDeletion"
		:enable-drag-handle="enableDragHandle"
		:name-placeholder="t('biblio', 'Name')"
		@update:name="onNameChange"
		@delete="onDelete">
		<NoSettings />
	</Field>
</template>

<script>
import FieldMixin from "../../mixins/FieldMixin";
import NoSettings from "./NoSettings.vue";

export default {
	name: "ShortTextField",
	mixins: [FieldMixin],
	components: {
		NoSettings,
	}
};
</script>
