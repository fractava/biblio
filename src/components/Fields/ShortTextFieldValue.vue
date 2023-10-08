<template>
	<div class="field__content">
		<NcInputField v-if="allowValueEdit"
			:placeholder="placeholder"
			:disabled="!allowValueEdit"
			:value="value"
			minlength="0"
			type="text"
			@update:value="onInput"
			@keydown.enter.exact.prevent="onKeydownEnter" />
		<p v-else>
			{{ value }}
		</p>
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
	props: {
		value: {
			type: String,
			default: "",
		},
	},
	methods: {
		onInput(newValue) {
			this.$emit("update:value", newValue);
		},
	},
};
</script>
