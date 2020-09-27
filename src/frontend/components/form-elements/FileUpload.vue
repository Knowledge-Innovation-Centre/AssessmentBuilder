<template>
  <div>
    <label for="label">
      {{ object.label }} <template v-if="object.required">*</template>
      <file-pond
          name="test"
          ref="pond"
          v-bind:allow-replace="true"
          v-bind:instant-upload="true"
          label-idle="Drop files here..."
          v-bind:allow-multiple="false"
          :server="server"
          v-bind:files="myFiles"
          v-on:init="handleFilePondInit"/>
    </label>
  </div>
</template>

<script>
// Import Vue FilePond
import vueFilePond from 'vue-filepond';
import axios from 'axios';


// Import image preview plugin styles
// import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';


// Import FilePond styles
import 'filepond/dist/filepond.min.css';

const FilePond = vueFilePond();
  export default {

    name: 'FileUpload',

    components: {
      FilePond
    },
    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
        show: false,
        myFiles: [],
        server: {
          process: (fieldName, file, metadata, load, error, progress, abort) => {

            // fieldName is the name of the input field
            // file is the actual file object to send
            const formData = new FormData();
            formData.append(fieldName, file, file.name);
            formData.append('action', 'aoat_upload_file');

            axios.post(aoat_config.ajax_url, formData, {
              processData: false,
              contentType: false,
              beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', aoat_config.nonce);
              },
            }).then((response) => {
              this.$store.dispatch('updateValue', { key: this.object.key, value: response.data})
              load(response);
            }).catch((response) => {
              console.log('error');
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
            }
          },
        }
      }
    },
    methods: {
      handleFilePondInit: function() {
        console.log('FilePond has initialized');

        // FilePond instance methods are available on `this.$refs.pond`
      }
    }
  };
</script>
<style scoped>

</style>
