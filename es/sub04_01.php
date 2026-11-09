<?php
include_once('./_common.php');
include_once(G5_PATH.'/plus/mail_sender.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$menuCodeParent = 4;
$menuCodeChild = 1;
include_once(G5_PATH.'/head.php');
?>
<div id="sub_wrap" class="sub04_01">
  <section class="sub-section section01 sub-container" data-aos="fade-up"  data-aos-duration="2000">


    <div class="send_box02">
      <fieldset>
        <form name="contactform" method="post" action="/es/sub04_01_send.php" enctype="multipart/form-data" class="fields">
          <ul>
            <!-- <h4>Basic Information</h4> -->
            <li>
              <label for="title" class="lbl required">Nombre de la Empresa</label>
              <div class="desc">
                <input name="title" type="text" class="input full" id="title" maxlength="50" required placeholder="Ingrese el nombre de su empresa para referencia de consulta (confidencial)">
              </div>
            </li>

            <li id="equipment_area">
              <span id="equipment" class="lbl">Equipo de Consulta</span>
              <div class="desc flex ">
                <div>
                  <input name="group[]" type="checkbox" id="surface" class="input full check" value="Inspector de Defectos Superficiales">
                  <p>Inspector de Defectos Superficiales</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="webcleaner" class="input full check" value="Limpiador Web">
                  <p>Limpiador Web</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="pinning" class="input full check"  value="Sistema de Pinning">
                  <p>Sistema de Pinning</p>
                </div>
                <div>
                  <input name="group[]" type="checkbox" id="etc_chk" class="input full check"  value="Otro (Entrada Directa)">
                  <p>Otro (Entrada Directa)</p>
                  <input name="group[]" type="text" class="input" id="etc_txt" maxlength="80">
                </div>
              </div>
            </li>

            <li>
              <label for="place" class="lbl required">Área de Instalación (o País)</label>
              <div class="desc">
                <input name="place" type="text" class="input full" id="place" maxlength="80" required placeholder="Ingrese el área de instalación (o país)">
              </div>
            </li>
            <li>
            <label for="email" class="lbl required">Correo Electrónico</label>
            <div class="desc">
              <input name="email" type="email" class="input full" id="email" maxlength="80" required placeholder="Ingrese su correo electrónico">
            </div>
          </li>
          <li>
              <label for="name" class="lbl required">Nombre (Persona de Contacto)</label>
              <div class="desc">
                <input name="name" type="text" class="input full" id="name" maxlength="50" required placeholder="Ingrese el nombre de la persona de contacto">
              </div>
            </li>
          <li>
            <label for="phone" class="lbl required">Número de Teléfono</label>
            <div class="desc">
            <input name="phone" type="tel" class="input full" id="phone" maxlength="30" required placeholder="Ingrese un número de teléfono disponible">
            </div>
          </li>

            <!-- Detail Info Toggle Button -->
            <li style="margin-top: 20px; margin-bottom: 10px;">
              <button type="button" id="toggleDetailBtn" style="background: #e0f0f0; border: 1px solid #ddd; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-size: 14px;">
                + Agregar Información Detallada
              </button>
            </li>

            <!-- Detail Info Area (Hidden by default) -->
            <div id="detailInfoArea" style="display: none; width: 100%; padding-bottom: 30px; border-bottom: 1px solid #ddd; margin-bottom: 30px;">
            <li>
              <label for="component" class="lbl">Componentes del Producto</label>
              <div class="desc">
                <input name="component" type="text" class="input full" id="component" maxlength="80" placeholder="ej., PET, PVC">
              </div>
            </li>
            <li>
              <label for="color" class="lbl">Color y Transparencia</label>
              <div class="desc">
                <input name="color" type="text" class="input full" id="color" maxlength="80">
              </div>
            </li>
            <li>
              <label for="area" class="lbl">Área Aplicada</label>
              <div class="desc">
                <input name="area" type="text" class="input full" id="area" maxlength="80" placeholder="Lado Único/Lado Doble">
              </div>
            </li>
              <li>
                <label for="thickness" class="lbl">Espesor</label>
                <div class="desc">
                  <input name="thickness" type="text" class="input full" id="thickness" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="width" class="lbl">Ancho</label>
                <div class="desc">
                  <input name="width" type="text" class="input full" id="width" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="lineSpeed" class="lbl">Velocidad de Línea</label>
                <div class="desc">
                  <input name="lineSpeed" type="text" class="input full" id="lineSpeed" maxlength="80" >
                </div>
              </li>
              <li>
                <label for="requests" class="lbl">Solicitud de Eliminación y Detección</label>
                <div class="desc">
                  <input name="requests" type="text" class="input full" id="requests" maxlength="80" placeholder="ej., Sílice, Cabello">
                </div>
              </li>
            </div>

            <!-- <h4>Consultation Details</h4> -->
            <li>
              <label for="comments" class="lbl">Mensaje</label>
              <div class="desc">
              <textarea type="text" class="input full" name="comments" id="comments" rows="10" cols="80" placeholder="Ingrese su consulta"></textarea>
              </div>
            </li>
            <li>
              <label for="file" class="lbl">Adjunto</label>
              <div class="desc">
                <input type="file" name="attachment" style="border: 0; padding:0; ">
              </div>
            </li>
            <li>
              <label for="agree" class="lbl">Recopilación y Uso de Información Personal</label>
              <div class="desc">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center;">
                    <input type="checkbox" name="agree" id="agree" required class="required" style="width: auto; margin-right: 8px;">
                    <label for="agree" style="margin: 0; display: inline;">Acepto la 'Recopilación y Uso de Información Personal'.</label>
                  </div>
                  <a href="javascript:void(0);" id="showPrivacyModal" style="color: #1a4691; text-decoration: underline; font-size: 13px; margin-left: 10px; cursor: pointer;">Recopilación y Uso de Información Personal</a>
                </div>
              </div>
            </li>
            <li>
              <label class="lbl">Verificación anti-spam</label>
              <div class="desc">
                <?php echo captcha_html(); ?>
              </div>
            </li>
          </ul>
          <div class="btn-group">
            <p><span>*</span> Los campos marcados con un asterisco (*) son obligatorios. Nos pondremos en contacto con usted tan pronto como confirmemos su consulta.</p>
            <button type="submit" class="btn-submit">Enviar</button>
           </div>
        </form>
      </fieldset>
    </div>
  </div>

  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const etcChk = document.getElementById("etc_chk");
    const etcTxt = document.getElementById("etc_txt");
    const toggleDetailBtn = document.getElementById("toggleDetailBtn");
    const detailInfoArea = document.getElementById("detailInfoArea");

    // Initially disable the text field
    etcTxt.disabled = true;

    // Event listener for checkbox
    etcChk.addEventListener("change", function() {
        if (this.checked) {
            etcTxt.disabled = false;  // Enable the text field
        } else {
            etcTxt.disabled = true;   // Disable the text field
            etcTxt.value = "";        // Clear the text field value
        }
    });

    // Toggle detail info area
    toggleDetailBtn.addEventListener("click", function() {
        if (detailInfoArea.style.display === "none") {
            detailInfoArea.style.display = "block";
            toggleDetailBtn.textContent = "- Contraer Información Detallada";
        } else {
            detailInfoArea.style.display = "none";
            toggleDetailBtn.textContent = "+ Agregar Información Detallada";
        }
    });

    // Show privacy policy modal
    const showPrivacyModal = document.getElementById("showPrivacyModal");
    
    if (showPrivacyModal) {
        showPrivacyModal.addEventListener("click", function(e) {
            e.preventDefault();
            const privacyContent = `<?php echo addslashes(nl2br(get_text($config['cf_privacy']))); ?>`;
            const modal = document.createElement('div');
            modal.id = 'privacyModal';
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100000; display: flex; align-items: center; justify-content: center;';
            modal.innerHTML = `
                <div style="background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; max-height: 80vh; overflow-y: auto; position: relative; width: 90%;">
                    <button onclick="document.getElementById('privacyModal').remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">×</button>
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">Recopilación y Uso de Información Personal</h3>
                    <div style="line-height: 1.6; color: #666;">${privacyContent}</div>
                    <button onclick="document.getElementById('privacyModal').remove()" style="margin-top: 20px; padding: 10px 20px; background: #1a4691; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Confirm</button>
                </div>
            `;
            document.body.appendChild(modal);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        });
    }
});
</script>

<?php
include_once(G5_PATH.'/tail.php');
?>
