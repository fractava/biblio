<template>
	<NcCheckboxRadioSwitch :checked="value"
		:disabled="true" />
</template>

<script>
import NcCheckboxRadioSwitch from "@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch";

import cellMixin from "./cellMixin.js";

export default {
	components: {
		NcCheckboxRadioSwitch,
	},
	mixins: [cellMixin],
};
</script>
