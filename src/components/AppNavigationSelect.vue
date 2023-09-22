<template>
    <div class="navigationSelectContainer">
        <vueSelect class="navigationSelectSelect" :options="['test', 'test 2']" />
    </div>
</template>

<script>
import vueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
//import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js';

export default {
	components: {
		vueSelect,
	},
};
</script>

<style scoped>
.navigationSelectContainer {
    margin-left: 22px;
    width: calc(100% - 44px);
    margin-right: 22px;
}

.navigationSelectSelect {
    min-width: 100%;
    width: 100%;
}
</style>