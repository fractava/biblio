<template>
	<div class="field__content">
		<NcInputField :placeholder="placeholder"
			:disabled="!allowValueEdit"
			:value="value"
			minlength="0"
			type="text"
			@update:value="onInput"
			@keydown.enter.exact.prevent="onKeydownEnter" />
	</div>
</template>

<script>
import NcInputField from "@nextcloud/vue/dist/Components/NcInputField.js";

import FieldValue from "../mixins/FieldValue.js";

export default {
	components: {
		NcInputField,
	},
	mixins: [FieldValue],
	methods: {
		onInput(newValue) {
			this.$emit("update:value", newValue);
		},
	},
};
</script>
