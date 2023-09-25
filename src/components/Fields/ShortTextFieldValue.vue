<template>
	<div class="field__content">
		<NcInputField :placeholder="t('biblio', 'Value')"
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

export default {
	components: {
		NcInputField,
	},
	props: {
		value: {
			type: String,
			default: "",
		},
		allowValueEdit: {
			type: Boolean,
			deafult: false,
		},
	},
	methods: {
		onInput(newValue) {
			this.$emit("update:value", newValue);
		},
	},
};
</script>
