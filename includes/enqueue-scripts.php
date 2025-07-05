<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'enqueue_custom_drag_drop_menu_scripts');

function enqueue_custom_drag_drop_menu_scripts() {
    ?>
    <style>
        #wp-admin-bar-plugins { position: relative; }
        #drop-area {
            position: relative;
            padding: 10px;
            text-align: center;
            border: 2px dashed #fff;
            border-radius: 10px;
            background: #00000057;
            z-index: 1000;
            margin: 7px;
            display: block;
            color: white;
        }
        #drop-area.drag-over {
            border-color: #333;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuLink = document.querySelector('#adminmenu #menu-plugins');
            var dropArea = document.createElement('div');
            dropArea.id = 'drop-area';
            dropArea.innerHTML = '<p>Glissez les extensions .zip ici</p><input type="file" id="fileElem" accept=".zip" multiple onchange="handleFiles(this.files)" style="display:none">';
            menuLink.appendChild(dropArea);
            
            var fileInput = document.getElementById('fileElem');

            var preventDefaults = (e) => {
                e.preventDefault();
                e.stopPropagation();
            };

            var uploadPlugin = (file) => {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('action', 'upload_plugin_action');
                    formData.append('_wpnonce', '<?php echo wp_create_nonce('upload_plugin_nonce'); ?>');

                    fetch(ajaxurl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Extension téléchargée avec succès: ' + file.name);
                            resolve(data.data.plugin_file);
                        } else {
                            console.log('Erreur avec ' + file.name + ': ' + data.data.message);
                            reject();
                        }
                    })
                    .catch(error => {
                        console.log('Erreur lors du téléchargement de l\'extension ' + file.name);
                        reject();
                    });
                });
            };

            var uploadPlugins = (files) => {
                if (files.length > 0) {
                    var promises = Array.from(files).map(file => uploadPlugin(file));

                    Promise.all(promises).then(pluginFiles => {
                        activatePlugins(pluginFiles);
                    }).catch(() => {
                        alert('Une ou plusieurs extensions n\'ont pas pu être téléchargées.');
                    });
                }
            };

            var activatePlugins = (pluginFiles) => {
                var formData = new FormData();
                formData.append('action', 'activate_plugins_action');
                formData.append('plugins', JSON.stringify(pluginFiles));
                formData.append('_wpnonce', '<?php echo wp_create_nonce('activate_plugins_nonce'); ?>');

                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Toutes les extensions ont été activées avec succès. Rechargement de la page.');
                        location.reload();
                    } else {
                        alert('Erreur lors de l\'activation des extensions: ' + data.data.message);
                    }
                })
                .catch(error => {
                    alert('Erreur lors de l\'activation des extensions.');
                });
            };

            var handleDrop = (e) => {
                var dt = e.dataTransfer;
                var files = dt.files;
                uploadPlugins(files);
            };

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                menuLink.addEventListener(eventName, preventDefaults, false);
            });
            menuLink.addEventListener('dragenter', () => dropArea.style.display = 'block', false);
            menuLink.addEventListener('dragleave', () => dropArea.style.display = 'block', false);
            dropArea.addEventListener('drop', handleDrop, false);
        });
    </script>
    <?php
}