<template>
	<NcDateTime :timestamp="value" :format="timeFormat" :relative-time="true" />
</template>
<script>
import NcDateTime from "@nextcloud/vue/dist/Components/NcDateTime.js";

export default {
	components: {
		NcDateTime,
	},
	props: ["value"],
	data() {
		return {
			timeFormat: {
				dateStyle: "short",
				timeStyle: "full",
			},
		};
	},
};
</script>
