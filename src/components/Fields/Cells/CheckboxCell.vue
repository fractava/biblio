<template>
	<NcCheckboxRadioSwitch :checked="value"
		:disabled="true" />
</template>

<script>
import NcCheckboxRadioSwitch from "@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js";

import cellMixin from "./cellMixin.js";

export default {
	components: {
		NcCheckboxRadioSwitch,
	},
	mixins: [cellMixin],
};
</script>
<style>
.checkbox-radio-switch__label:has(.checkbox-radio-switch__input:disabled), .checkbox-radio-switch__label:has(.checkbox-radio-switch__input:disabled) * {
	cursor: initial;
}
</style>
