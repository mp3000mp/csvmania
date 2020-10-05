/**
 * Ici les scripts génériques présents partout
 */

// librairies
//var $ = require('jquery');
require('bootstrap-sass');

// slider
require('rangeslider.js');

// date picker
require('flatpickr');
require('flatpickr/dist/l10n/fr.js');
require('flatpickr/dist/l10n/es.js');

// customed
require('../css/app.scss');



// on document ready
$(function () {









    /* drag and drop file (!!! ne marche qu'avec un seul input !!!) */
    function oDndFile(selector) {

        // constructor
        var alreadyUploaded = false;
        var dndFile = document.getElementsByClassName(selector);

        if(dndFile.length){
            var that = this;


            // handle default value
            this.setData = function(data64){
                var data = atob(data64);
                if(/^([^\r\n]*);([^\r\n;]+)$/gm.test(data)){
                    console.log(data)
                    var nbRow = (data.match(/[\r\n]/g) || []).length;
                    dndTextarea.value = data64;
                    var span = dndHolder.getElementsByTagName('span')[1];
                    span.innerHTML = span.innerHTML.replace('%n', nbRow);
                    dndHolder.className = 'form-control dndholder dndsuccess';
                    alreadyUploaded = true;
                }else{
                    dndHolder.className = 'form-control dndholder dndbadformat';
                }
            }
            // read file
            this.readFile = function (file) {
                var reader = new FileReader();
                reader.onload = function (event){
                    // check format
                    var data64 = event.target.result.split('base64,')[1];
                    that.setData(data64);
                }
                reader.readAsDataURL(file[0]);
            }


            // group element
            dndFile = dndFile[0];

            // find form and hidden input file
            var dndForm = dndFile.parentElement;
            var dndInputFile = null;
            while(dndForm.tagName.toUpperCase() != 'FORM'){
                if(dndInputFile == null && dndForm.className.indexOf('form-group') >= 0){
                    dndInputFile = dndForm.getElementsByTagName('input')[0];
                }
                dndForm = dndForm.parentElement;
            }

            // drag and drop event
            var dndHolder = dndFile.getElementsByClassName('dndholder')[0];
            dndHolder.ondragover = function () {
                if(!alreadyUploaded){
                    this.className = 'form-control dndholder dndhover';
                }
                return false;
            };
            dndHolder.ondragend = function () {
                if(!alreadyUploaded){
                    this.className = 'form-control dndholder dnddefault';
                }
                return false;
            };
            dndHolder.ondragstart = function () {
                if(!alreadyUploaded){
                    this.className = 'form-control dndholder dndhover';
                }
                return false;
            };
            dndHolder.ondragleave = function () {
                if(!alreadyUploaded){
                    this.className = 'form-control dndholder dnddefault';
                    return false;
                }
            };
            dndHolder.ondrop = function (e) {
                if(!alreadyUploaded){
                    this.className = 'form-control dndholder dnddefault';
                    e.preventDefault();
                    that.readFile(e.dataTransfer.files);
                }else{
                    return false;
                }
            };
            dndHolder.onclick = function(){
                dndInputFile.click();
            }

            dndInputFile.onchange = function() {
                that.readFile(this.files);
            }

            // find textarea
            var dndTextarea = dndFile.getElementsByTagName('textarea')[0];
            if(dndTextarea.value != ''){
                this.setData(dndTextarea.value);
            }

            // on met rouge si required et vide
            if(dndTextarea.required == true){
                dndTextarea.required = false;
                dndForm.onsubmit = function(){
                    if(!alreadyUploaded){
                        dndHolder.className = 'form-control dndholder dndrequired';
                        return false;
                    }
                }
            }
        }
    }
    var oDND = new oDndFile('dndFile');













    /**
     * flatpickr : https://flatpickr.js.org/
     * @type {*|Instance|Instance[]}
     */
    const fp = flatpickr(".flatpickr", {
        locale: document.documentElement.lang,
        allowInput: false,
        // on met à jour les champs cachés à la sélection d'une date
        onChange: function(selectedDates, dateStr, instance){
            var d = selectedDates[0];
            var elName = instance.input.name;
            document.getElementById(elName+'_day').value = d.getDate();
            document.getElementById(elName+'_month').value = d.getMonth()+1;
            document.getElementById(elName+'_year').value = d.getFullYear();
        }
    });





    /**
     * set progress bar width for animation
     * @param selector
     */
    function oProgressBar(selector){

        this.draw = function(el){

            var w = Math.round(100*(el.getAttribute('aria-valuenow')/el.getAttribute('aria-valuemax'))*100)/100;
            el.style.width = w+'%';
            el.innerHTML = '<span>' + w + '%</span>';
            el.parentElement.setAttribute('data-original-title', el.getAttribute('aria-valuenow') + '/' + el.getAttribute('aria-valuemax'));
            if(w==100){
                el.style.backgroundColor = '#5bcc36';
            }else{
                el.style.backgroundColor = '#337ab7';
            }
            if(w==0){
                el.style.color = 'black';
            }else{
                el.style.color = 'black';
            }

        }



        this.updateBar = function(id, i){
            var el = document.getElementById(id);
            el.setAttribute('aria-valuenow', parseInt(el.getAttribute('aria-valuenow')) + i);
            this.draw(el);
        }

        // constructor
        var pbs = document.getElementsByClassName(selector);
        var i = 0;
        while(i < pbs.length){
            var pb = pbs[i];
            this.draw(pb);
            i++;
        }

    }
    var oPB = new oProgressBar('progress-bar');







    /* bootstrap tooltype */
    $('.mptooltype').tooltip();


    // slider
    function RS_upd_color(val, left, right){
        var r1 = 204+(204-194)*(1-val/100);
        var g1 = 86+(204-86)*(val/100);
        var b1 = 54+(54-43)*(1-val/100);
        var r2 = 91+(204-91)*(1-val/100);
        var g2 = 204+(204-204)*(val/100);
        var b2 = 54+(54-54)*(val/100);
        left.css('background', 'linear-gradient(to right, rgb('+r1+','+g1+','+b1+'), rgb('+r2+','+g2+','+b2+')');
        //right.css('background', 'linear-gradient(to left, #5bcc36, #cc562b)');
    }

    $('input[type="range"]').rangeslider({
        polyfill: false,
        onInit: function(){
            RS_upd_color(50, this.$fill, this.$range);
        },
        onSlide: function(pos, val){
            RS_upd_color(val, this.$fill, this.$range);
        }
    });











    // mettre id talbeau puis callbakc pour mettre à jour les bars de prog
    function ompTableAjax(tableDOM, cb){

        tableDOM.find('input:text').change(function(){
            var el = $(this);
            var val = el.val();
            var json = {value: val};

            // get all table, tr,td and input data attributes
            $.each(el.closest('table').data(), function(key, val) {
                json[key]= val;
            });
            $.each(el.closest('tr').data(), function(key, val) {
                json[key]= val;
            });
            $.each(el.closest('td').data(), function(key, val) {
                json[key]= val;
            });
            $.each(el.data(), function(key, val) {
                json[key]= val;
            });

            mpAjax(json.url, 'POST', json, function(err,res){
                var colorBef = el.css('background-color');
                if(!err){
                    var color = res.error=='' ? 'green' : 'red';
                    if(res.error!=''){
                        alert(res.error);
                    }

                }else{
                    var color = 'red';
                }

                el.css('background-color', color);
                setTimeout(function(){
                    el.css('background-color', colorBef)
                }, 750);

                // callback success
                cb(res);

            });
        });
    }

    var el = $('#tokenedit');
    if(el.length){
        var ote = new ompTableAjax(el, function(res){
            console.log('cb' + ' - ' + JSON.stringify(res));
            if(res.action == 'notif1_admin'){
                oPB.updateBar('barValidated', res.value);
            }else if(res.action == 'notif1'){
                oPB.updateBar('barDone', res.value);
            }
        });
    }





    /* !!! ne marche qu'avec un seul élément
     * class pour gérer les interaction entre le form type Fields et le user
     * en gros on click sur un label, ça coche/décoche la checkbox cachée
     */
    function ompFields(selector){

        function getElPosition(child, parent, tag){
            var children = parent.getElementsByTagName(tag);
            var i =0;
            while(i<children.length){
                if(child == children[i]){
                    return i;
                }
                i++;
            }
        }

        var el = document.getElementsByClassName(selector);
        if(el.length){
            el = el[0];
            var hiddenCBs = el.getElementsByTagName('option');
            var ui = el.getElementsByClassName('mpFieldsUI')[0];
            var labels = ui.getElementsByTagName('span');
            var i = 0;
            while(i<labels.length){
                labels[i].onclick = function(){
                    var n = getElPosition(this, ui, 'span');
                    hiddenCBs[n].selected = !hiddenCBs[n].selected;
                    this.className = hiddenCBs[n].selected ? 'badge badge-success' : 'badge badge-secondary';
                }
                i++;
            }
        }
    }
    var oMPF = new ompFields('mpFields');



















    /* ajax */
    function mpAjax(url, method, json, cb){
        $.ajax({
            url: url,
            data: json,
            dataType: 'json',
            method: method,
            success: function(res){
                cb(0,res);
            },
            error: function(res){
                console.log('error '+res.status+' '+res.statusText);
                console.log(res.responseText);
                cb(1,res.responseText);
            }
        });
    }




});











