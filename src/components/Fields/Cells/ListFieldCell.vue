<template>
	<span>{{ value.join(", ") }}</span>
</template>

<script>
import cellMixin from "./cellMixin.js";

export default {
	mixins: [cellMixin],
};
</script>
