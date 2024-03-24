<template>
	<NcDateTime v-if="timestamp !== false" :timestamp="timestamp" />
</template>
<script>
import NcDateTime from "@nextcloud/vue/dist/Components/NcDateTime.js";
// import { getCanonicalLocale } from "@nextcloud/l10n";

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
	computed: {
		timestamp() {
			if (Number.isInteger(this.value)) {
				return this.value * 1000;
			} else {
				return false;
			}
		},
		/* absoluteTime() {
			const formatter = new Intl.DateTimeFormat(getCanonicalLocale(), {
				dateStyle: "short",
				timeStyle: "full",
				timeZoneName: "short",
			});
			return formatter.format(new Date(this.timestamp));
		}, */
	},
};
</script>
