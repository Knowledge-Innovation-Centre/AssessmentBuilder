<template>
  <div class="app-settings">
    <h2>Apprenticeship online assessment tool settings</h2>
    <table>
      <tbody>
      <tr v-for="setting in settings" :key="setting.key">
        <th class="aoat-text-right">{{ setting.label }}</th>
        <td>
          <template v-if="setting.key === 'aoat_page_for_assessments'">
            <multiselect
                v-model="setting.value"
                :multiple="false"
                label="post_title"
                placeholder="Select one"
                class="aoat-w-full"
                track-by="ID"
                :options="pages">
            </multiselect>
          </template>
          <template v-else>
            <input type="text" v-model="setting.value" />
          </template>
        </td>
      </tr>
      </tbody>
    </table>

    <button @click="saveSettings()"
            class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-py-2 aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow">
      Save settings
    </button>
  </div>
</template>

<script>
import axios from "axios";
import {Multiselect} from "vue-multiselect";

export default {

  name: 'Settings',

  components: {
    Multiselect
  },

  data () {
    return {
      settings: [],
      pages: []
    };
  },

  mounted() {
    this.loadData();
  },

  methods: {
    loadData() {
      axios.get(aoat_config.aoatGetSettingsUrl).then((result) => {
        this.settings = result.data
      })
      axios.get(aoat_config.aoatGetPagesUrl).then((result) => {
        this.pages = result.data
      })
    },
    saveSettings() {
      let $this = this
      axios.post(aoat_config.aoatSaveSettingsUrl, {
        settings: this.settings
      })
      .then(() => {
        $this.$notify({
          title: 'Settings saved',
          type: 'success',
        })
      })
      .catch(function (error) {

        $this.$notify({
          title: 'Something went wrong',
          type: 'error',
        })
        console.log(error);
      });
    }
  }
};
</script>

<style lang="css" scoped>
</style>
