<template>
  <div>
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <file-pond
      ref="pond"
      name="test"
      class="aoat-w-full"
      :allow-replace="true"
      :instant-upload="true"
      label-idle="Drop files here..."
      :allow-multiple="true"
      :server="server"
      :allow-file-type-validation="allowFileTypeValidation"
      :accepted-file-types="acceptedFileTypes"
      :files="files"
      :max-files="maxFiles"
      :max-file-size="maxSize"
      :style="getWidthStyle"
      :class="hasError ? 'aoat-border-red-400' : ''"
      @init="handleFilePondInit"
      @processfile="updateValue()"
      @removefile="updateValue()"
    />
  </div>
</template>

<script>
// Import Vue FilePond
import vueFilePond from "vue-filepond";
import Api from "../../Api";

// Import the plugin code
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
// Import the plugin code
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize
);
export default {
  name: "FileUpload",

  components: {
    FilePond
  },
  props: {
    object: {
      type: Object,
      required: true
    },
    hasError: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data() {
    return {
      show: false,
      files: [],
      fileIds: [],
      server: {
        process: (fieldName, file, metadata, load, error, progress, abort) => {
          // fieldName is the name of the input field
          // file is the actual file object to send
          const formData = new FormData();
          formData.append(fieldName, file, file.name);
          formData.append("action", "aoat_upload_file");

          Api.post(aoat_config.ajax_url, formData, {
            processData: false,
            contentType: false
          })
            .then(response => {
              load(response.data);
            })
            .catch(response => {
              error(response); // not sure if that contains the response message
            });

          // Should expose an abort method so the request can be canceled
          return {
            abort: () => {
              // This function is entered if the user has tapped the cancel button
              // request.abort();

              // Let FilePond know the request has been cancelled
              abort();
            }
          };
        }
      }
    };
  },

  computed: {
    acceptedFileTypes() {
      if (!this.object.acceptedFileTypes) {
        return [];
      }
      return this.object.acceptedFileTypes;
    },
    allowFileTypeValidation() {
      return !!this.acceptedFileTypes.length;
    },
    maxFiles() {
      if (!this.object.maxFiles) {
        return 1;
      }
      return this.object.maxFiles;
    },
    maxSize() {
      if (!this.object.maxSize) {
        return null;
      }
      return this.object.maxSize + "MB";
    },
    getWidthStyle() {
      if (this.object.maxWidth) {
        return (
          "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ";"
        );
      }

      return "";
    }
  },
  methods: {
    handleFilePondInit: function() {
      console.log("FilePond has initialized");

      // FilePond instance methods are available on `this.$refs.pond`
    },
    handleFileRemoveFile: function() {
      this.updateValue();
    },
    updateValue() {
      let fileIds = this.$refs.pond
        .getFiles()
        .map(pondFile => parseInt(pondFile.serverId));

      this.$store.dispatch("updateValue", {
        key: this.object.key,
        value: fileIds
      });
    }
  }
};
</script>
<style scoped></style>
