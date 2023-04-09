<!--
  - @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @author Simon Vieille <contact@deblan.fr>
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
		@update:title="onTitleChange"
		@delete="onDelete">
		<div class="field__content">
			<NcDatetimePicker :value="value"
				value-type="format"
				:disabled="!allowValueEdit"
				:formatter="formatter"
				:placeholder="fieldType.valuePlaceholder"
				:show-second="false"
				:type="datetimePickerType"
				:input-attr="inputAttr"
				@change="onValueChange" />
		</div>
	</Field>
</template>

<script>
import moment from "@nextcloud/moment";

import FieldMixin from "../../mixins/FieldMixin";
import NcDatetimePicker from "@nextcloud/vue/dist/Components/NcDatetimePicker";

export default {
	name: "DateField",

	components: {
		NcDatetimePicker,
	},

	mixins: [FieldMixin],

	data() {
		return {
			time: null,
			formatter: {
				stringify: this.stringify,
				parse: this.parse,
			},
		};
	},

	computed: {
		// Allow picking time or not, depending on variable in fieldType.
		datetimePickerType() {
			return this.fieldType.includeTime ? "datetime" : "date";
		},

		/**
		 * Calculating the format, that moment should use. With or without time.
		 *
		 * @return {string}
		 */
		getMomentFormat() {
			if (this.datetimePickerType === "datetime") {
				return "LLL";
			}
			return "LL";
		},

		/**
		 * All non-exposed props onto datepicker input-element.
		 *
		 * @return {object}
		 */
		inputAttr() {
			return {
				required: this.isRequired,
			};
		},
	},

	methods: {
		/**
		 * DateTimepicker show date-text
		 * Format depends on component-type date/datetime
		 *
		 * @param {Date} date the selected datepicker Date
		 * @return {string}
		 */
		stringify(date) {
			return moment(date).format(this.getMomentFormat);
		},
		/**
		 * Reinterpret the stringified date
		 *
		 * @param {string} dateString Stringified date
		 * @return {Date}
		 */
		parse(dateString) {
			return moment(dateString, this.getMomentFormat).toDate();
		},

		/**
		 * Store Value
		 *
		 * @param {string} dateString The parsed string to store
		 */
		onValueChange(dateString) {
			this.$emit("update:value", dateString);
		},
	},
};
</script>

<style lang="scss" scoped>
.mx-datepicker {
	// Enlarging a bit (originally 210px) to have enough space for placeholder
	width: 250px;
}
</style>
