<template>
  <div class="app-settings">
    <h2>Apprenticeship online assessment tool settings</h2>
    <table class="aoat-w-full">
      <tbody>
        <template v-for="setting in settings">
          <tr :key="setting.key + '_title'">
            <th class="aoat-text-left">{{ setting.label }}</th>
          </tr>
          <tr :key="setting.key + '_value'">
            <td>
              <template v-if="setting.key === 'aoat_page_for_assessments'">
                <multiselect
                  v-model="setting.value"
                  :multiple="false"
                  label="post_title"
                  placeholder="Select one"
                  class="aoat-w-full"
                  track-by="ID"
                  :options="pages"
                />
              </template>
              <template
                v-else-if="
                  [
                    'aoat_redirect_after_completion',
                    'aoat_show_link_button',
                    'aoat_use_loc'
                  ].includes(setting.key)
                "
              >
                <input v-model="setting.value" type="checkbox" />
              </template>
              <template v-else-if="['available_colors'].includes(setting.key)">
                <div
                  v-for="(settingValue, index) in setting.value"
                  :key="index"
                >
                  <input
                    :key="index"
                    v-model="setting.value[index]"
                    type="text"
                  />

                  <button @click="removeColor(setting, index)">
                    Remove color
                  </button>
                </div>
                <button @click="addColor(setting)">Add color</button>
              </template>
              <template
                v-else-if="['aoat_email_content'].includes(setting.key)"
              >
                <textarea v-model="setting.value" rows="10" cols="50" />
              </template>
              <template v-else>
                <input v-model="setting.value" type="text" />
              </template>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <button
      class="aoat-bg-white aoat-hover:bg-gray-100 aoat-text-gray-800 aoat-font-semibold aoat-py-2 aoat-px-4 aoat-border aoat-border-gray-400 aoat-rounded aoat-shadow"
      @click="saveSettings()"
    >
      Save settings
    </button>
  </div>
</template>

<script>
import Api from "../Api";
import { Multiselect } from "vue-multiselect";

export default {
  name: "Settings",

  components: {
    Multiselect
  },

  data() {
    return {
      settings: [],
      pages: []
    };
  },

  mounted() {
    this.loadData();
  },

  methods: {
    async loadData() {
      await Api.get(aoat_config.aoatGetPagesUrl).then(result => {
        this.pages = result.data;
      });
      await Api.get(aoat_config.aoatGetSettingsUrl).then(result => {
        this.fillSettings(result.data);
      });
    },
    fillSettings(responseData) {
      this.settings = [];
      for (let setting of responseData) {
        if (setting.key === "aoat_page_for_assessments") {
          if (typeof setting.value !== "object") {
            setting.value = this.pages.find(
              page => page.guid === setting.value
            );
          }
          this.settings.push(setting);
        } else {
          this.settings.push(setting);
        }
      }
    },
    prepareSettings() {
      let settings = [];
      for (let setting of this.settings) {
        if (setting.key === "aoat_page_for_assessments") {
          let newSetting = JSON.parse(JSON.stringify(setting));
          if (setting.value) {
            newSetting.value = setting.value.guid;
          } else {
            newSetting.value = null;
          }
          settings.push(newSetting);
        } else {
          settings.push(setting);
        }
      }
      return settings;
    },
    saveSettings() {
      let $this = this;
      Api.post(aoat_config.aoatSaveSettingsUrl, {
        settings: this.prepareSettings()
      })
        .then(() => {
          $this.$notify({
            title: "Settings saved",
            type: "success"
          });
        })
        .catch(function() {
          $this.$notify({
            title: "Something went wrong",
            type: "error"
          });
        });
    },
    addColor(setting) {
      setting.value.push("");
    },
    removeColor(setting, index) {
      setting.value.splice(index, 1);
    }
  }
};
</script>

<style lang="css" scoped>
th {
  max-width: 300px;
}
</style>
