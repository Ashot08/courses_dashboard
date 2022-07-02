const loaderHtml = `<div class="cd__loader">
                        <svg width="60px" height="60px" version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                            <path fill="#f80000" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                            </path>
                        </svg>
                    </div>`;

const data = {
    66: 'Организация оказания первой помощи',
    52: 'Обучение по общим вопросам охраны труда и функционирования системы управления охраной труда',
    53: 'Обучение безопасным методам и приемам выполнения работ при воздействии вредных и (или) опасных производственных факторов, опасностей, идентифицированных в рамках СУОТ в организации и ОПР',
    chaptersTitles: {},
    fullCourses: new Set(),
    chapters: {
        85: [52, 54, 55, 56, 63, 64, 65, 67, 68, 81, 53, 58, 59, 61, 62, 69, 78, 79, 80, 83],
        66: [72, 73, 74, 75, 76, 77]
    },
    alwaysChecked: [66, 52, 53]
}


function cd__content_request (id, action ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                id
            }
        },
    )
}
function cd__key_request (key, action ) {
    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                key
            }
        },
    )
}

/*----------Tabs Routing----------*/

function loadContent(action = 'programs'){
    const result_block = jQuery('.cd__tabs_content');
    let user_id = jQuery('.cd__tabs').data('user_id');

    if(!user_id){user_id = 1}

    switch (action){
        case 'programs':

            cd__content_request (user_id, 'cd__get_director_programs_list' ).then(
                res => result_block.html(res)
            )
            break;
        case 'students_control':
            cd__content_request (user_id, 'cd__get_students_control_programs_list' ).then(
                res => result_block.html(res)
            )
            break;
        case 'keys':
            cd__content_request (user_id, 'cd__get_keys_programs_list' ).then(
                res => result_block.html(res)
            )
            break;
        case 'profile':
            cd__content_request (user_id, 'cd__get_profile' ).then(
                res => result_block.html(res)
            )
            break;
    }
    jQuery('.cd__tabs_nav_item').each(function(){
        
        if(jQuery(this).data('tab') === action){
            jQuery(this).addClass('active');
        }
    })

}

jQuery(document).on('click', '.cd__tabs_nav_item',  function(){
    if ('URLSearchParams' in window) {
        var searchParams = new URLSearchParams(window.location.search);
        searchParams.set("tab", jQuery(this).data('tab'));
        window.location.search = searchParams.toString();
    }
})

jQuery(document).ready(function(){
    const tabName = getUrlParameter('tab');
    if(!tabName || tabName === 'programs'){
        loadContent()
    }else{
        loadContent(tabName);
    }
})

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
}

/*--------------------------------*/


/*--------Create Program----------*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="show_create_program"]', function(){
       const resultBlock = jQuery(this).parent();
        resultBlock.html(loaderHtml);
        cd__content_request (1, 'cd__get_create_program_view' ).then(res=>resultBlock.html(res));
    })
})
jQuery(document).on('click', '.cd__program_save', function(){
    const result_block = jQuery('.cd__steps_warnings');
    let director_id = jQuery('.cd__tabs').data('user_id');
    if(!director_id){director_id = 1}

    const name = jQuery('.cd__program_name_input').val();
    const description = jQuery('.cd__program_description_input').val();
    data.fullCourses = new Set();
    jQuery('.cd__program_checkbox').each(function (i){
        const input  = jQuery(this);
        if(input.is(':checked')){

            data.fullCourses.add(input.data('course_id'));
            delete data.chapters[input.data('course_id')];

        }
    })
    console.log(data);

    jQuery(this).html(`<svg width="30px" height="30px" version="1.1" id="L3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
<circle fill="none" stroke="#fff" stroke-width="4" cx="50" cy="50" r="44" style="opacity:0.5;"></circle>
  <circle fill="#fff" stroke="#e74c3c" stroke-width="3" cx="8" cy="54" r="6">
    <animateTransform attributeName="transform" dur="2s" type="rotate" from="0 50 48" to="360 50 52" repeatCount="indefinite"></animateTransform>
    
  </circle>
</svg>`);
    cd__create_program_request(director_id, name, description, 'cd__create_new_program', data).
    then((res) => {
        if(res === 'errorName'){
            result_block.html('<div>Вы не указали название для новой учебной программы</div>');
            result_block.bounce({
    			interval: 100,
    			distance: 3,
    			times: 15
    		});
            
            jQuery('html, body').animate({ scrollTop: 0 }, "fast");
        }else if(res === 'errorCoursesIds'){
            result_block.html('<div>Нужно выбрать хотя бы один курс для новой учебной программы</div>');
            result_block.bounce({
    			interval: 100,
    			distance: 3,
    			times: 15
    		});
            jQuery('html, body').animate({ scrollTop: 0 }, "fast");
        }else if(res === 'success'){
            result_block.html('<div class="success">Новая учебная программа "' + name + '" успешно добавлена</div>');
            jQuery('html, body').animate({ scrollTop: 0 }, "slow");
            jQuery('.cd__create_program').html('<button data-action="show_create_program">Создать программу</button>');
            loadContent('programs');
        }
        setTimeout(()=>{
            jQuery(this).html('Создать');
        }, 500)
        
    })
})

/*--------Create Separate Program----------*/

jQuery(document).on('click', '.cd__program_separate_save', function(){



    const result_block = jQuery('.cd__steps_warnings');
    let director_id = jQuery('.cd__tabs').data('user_id');
    if(!director_id){director_id = 1}

    const name = jQuery('.cd__program_name_input').val();
    const description = jQuery('.cd__program_description_input').val();
    data.fullCourses = new Set();
    jQuery('.cd__program_checkbox').each(function (i){
        const input  = jQuery(this);
        if(input.is(':checked')){

            data.fullCourses.add(input.data('course_id'));
            delete data.chapters[input.data('course_id')];

        }
    })

    // for(let item of data.alwaysChecked){
    //     data.chapters[item] = [item];
    // }
    console.log(data)
    /*------------Собираем имена--------------*/

    jQuery('.cd__programs_list > .cd__programs_item').each(function(){
        data[jQuery(this).data('course_id')] = jQuery(this).find('.cd__programs_item_title').html();
    })

    /*----------------------------------------*/
     if(!jQuery.isEmptyObject(data.chapters)){

        jQuery(this).html(`<svg width="30px" height="30px" version="1.1" id="L3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
        <circle fill="none" stroke="#fff" stroke-width="4" cx="50" cy="50" r="44" style="opacity:0.5;"></circle>
          <circle fill="#fff" stroke="#e74c3c" stroke-width="3" cx="8" cy="54" r="6">
            <animateTransform attributeName="transform" dur="2s" type="rotate" from="0 50 48" to="360 50 52" repeatCount="indefinite"></animateTransform>
            
          </circle>
        </svg>`);

        Object.keys(data.chapters).forEach(function(key) {

            cd__create_separate_programs_request(director_id, name, description, 'cd__create_new_separate_programs', this[key], data[key])
                .then((res) => {
                if(res === 'errorName'){
                    result_block.html('<div>Вы не указали название для новой учебной программы</div>');
                    result_block.bounce({
                        interval: 100,
                        distance: 3,
                        times: 15
                    });

                    jQuery('html, body').animate({ scrollTop: 0 }, "fast");

                }else if(res === 'errorCoursesIds'){
                    result_block.html('<div>Нужно выбрать хотя бы один курс для новой учебной программы</div>');
                    result_block.bounce({
                        interval: 100,
                        distance: 3,
                        times: 15
                    });
                    jQuery('html, body').animate({ scrollTop: 0 }, "fast");


                }else if(res === 'success'){
                    result_block.html('<div class="success">Учебные программы успешно созданы</div>');
                    jQuery('html, body').animate({ scrollTop: 0 }, "slow");
                    jQuery('.cd__create_program').html('<button data-action="show_create_program">Создать программу</button>');
                    loadContent('programs');



                }
                setTimeout(()=>{
                    jQuery('.cd__program_separate_save').html('Создать по отдельности');
                }, 500)

            })

        }, data.chapters);

    }else {
        result_block.html('<div>Нужно выбрать хотя бы один курс для новой учебной программы</div>');
        result_block.bounce({
            interval: 100,
            distance: 3,
            times: 15
        });
        jQuery('html, body').animate({ scrollTop: 0 }, "fast");
    }


})


jQuery(document).on('click', '.cd__step_toggler', function(){
    jQuery('.cd__step').slideToggle();
})

function cd__create_program_request (director_id = 1, name= '', description= '', action, courses ) {

    let courses_lvl_2 = [];
    const chaptersArr = Object.keys(courses.chapters).map(function(k){return courses.chapters[k]});
    for(let arr of chaptersArr){
        courses_lvl_2 = courses_lvl_2.concat(arr);
    }

    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                director_id,
                name,
                description,
                courses_lvl_1: Array.from(courses.fullCourses),
                courses_lvl_2,
                alwaysChecked: courses.alwaysChecked
            }
        },
    )
}

function cd__create_separate_programs_request (director_id = 1, name= '', description= '', action, courses, surname ) {

    return jQuery.ajax(
        {
            method: 'Post',
            url: ajaxUrl.url,
            data: {
                action,
                director_id,
                name,
                description,
             //   courses_lvl_1: Array(66, 52, 53),
                courses_lvl_2: courses,
                surname
            }
        },
    )
}


	jQuery.fn.shake = function (settings) {
		settings = jQuery.extend({
			interval: 100,
			distance: 10,
			times: 4,
			complete: jQuery.noop
		}, settings);

		var $this = jQuery(this);

		for (var i = 0; i < settings.times + 1; i++) {
			$this.transition(
				{
					x: i % 2 == 0 ? settings.distance : settings.distance * -1
				},
				settings.interval
			);
		}

		$this.transition({ x: 0 }, settings.interval, function () {
			$this.removeAttr("style");
			settings.complete.call($this[0]);
		});
	};
	jQuery.fn.bounce = function (settings) {
		settings = jQuery.extend({
			interval: 100,
			distance: 10,
			times: 4,
			complete: jQuery.noop
		}, settings);

		var $this = jQuery(this);

		for (var i = 0; i < settings.times + 1; i++) {
			jQuery(this).transition(
				{ y: i % 2 == 0 ? settings.distance : settings.distance * -1 },
				settings.interval
			);
		}

		jQuery(this).transition({ y: 0 }, settings.interval, function () {
			$this.removeAttr("style");
			settings.complete.call($this[0]);
		});
	};

/*--------------------------------*/


/*-------Render chapters list------*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="chose_by_chapter"]', function(){
        const course_id = jQuery(this).data('course_id');
        const result_block = jQuery('.cd__modal_result');
        result_block.html(loaderHtml);
        cd__content_request (course_id, 'cd__get_chapters_list' ).then(res=>result_block.html(res)).then(()=>{
            for(let chapter_key in data.chapters){
                if (data.chapters.hasOwnProperty(chapter_key)) {
                    for(chapter_id of data.chapters[chapter_key]){
                        jQuery(`input[data-chapter_id="${chapter_id}"]`).prop('checked', true);
                    }
                }
            }
            jQuery(document).ready(function(){
                jQuery('.cd__chapters_list_level_1').treeview({
                    collapsed: true,
                    animated: 'medium',
                    unique: false
                });
            });
            for(let item of data.alwaysChecked){
                console.log(jQuery(`.cd__chapters[data-course_id="${item}"]`))
                jQuery(`.cd__chapters[data-course_id="${item}"] .cd__chapters_list_item_input`).prop('checked', true);
                jQuery(`.cd__chapters[data-course_id="${item}"] .cd__chapters_list_item_input`).prop('disabled', true);


                jQuery(`.cd__chapters_list_item[data-chapter_id="${item}"]`).find('.cd__chapters_list_item_input').prop('checked', true);
                jQuery(`.cd__chapters_list_item[data-chapter_id="${item}"]`).find('.cd__chapters_list_item_input').prop('disabled', true);
            }
        });


    })
})

/*----------------------------------*/


jQuery(document).on('change', '.cd__program_checkbox', function(){
    const course_id = jQuery(this).data('course_id');
    if(jQuery(this).is(':checked')){
        delete data.chapters[course_id];
        jQuery(`.cd__programs_item[data-course_id="${course_id}"] .cd__chose_by_chapter_result`).html('');
    }
})



/*--------Manual Chose chapters-------*/




jQuery(document).ready(function(){

    jQuery(document).on('click', '[data-action="submit_chosen_chapters"]', function(){
        const course_id = jQuery(this).data('course_id');
        data.chapters[course_id] = [];
        const resulbBlock = jQuery(`.cd__programs_item[data-course_id=${course_id}] .cd__chose_by_chapter_result`);
        resulbBlock.html('');
        jQuery(`.cd__chapters[data-course_id="${course_id}"] .cd__chapters_list_item_input:checked`).each(
            function(){
                data.chapters[course_id].push(jQuery(this).data('chapter_id'));
                resulbBlock.html(`<div>Количество выбранных разделов: ${data.chapters[course_id].length}</div>`);
            }
        )
        if(data.chapters[course_id].length){
            jQuery(`.cd__program_checkbox[data-course_id="${course_id}"]`).prop('checked', false);
        }
        console.dir(data)
    })

    jQuery(document).on('change', `.cd__chapters_list_item_input`, function(){
        const root_course_id = jQuery(this).data('root_course_id');

        if(jQuery(this).is(':checked')){
            const course_id = jQuery(this).data('chapter_id');
            jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                .find(`.cd__chapters_list_item[data-chapter_id="${course_id}"] .cd__chapters_list_item_input`)
                .prop('checked', true);
        }else{
            const course_id = jQuery(this).data('chapter_id');
            let parent_id = jQuery(this).data('parent_id');
            while(parent_id){
                const checkbox = jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                    .find(`.cd__chapters_list_item_input[data-chapter_id="${parent_id}"]`);
                checkbox.prop('checked', false);
                parent_id = checkbox.data('parent_id');

            }

            jQuery(`.cd__chapters[data-course_id="${root_course_id}"]`)
                .find(`.cd__chapters_list_item[data-chapter_id="${course_id}"] .cd__chapters_list_item_input`)
                .prop('checked', false);
        }
    })

})



/*------------------------------------*/



/*-------Program List Item-------*/

jQuery(document).on('click', '.cd_program .cd__programs_item', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__program_details');
    const title = jQuery(this).find('.cd__programs_item_title').html();
    cd__content_request (program_id, 'cd__get_program_details' ).then((res) => {
        if(res){
            result_block.html(`<div class="success">Программа <span>"${title}"</span> состоит из следующих курсов: </div>`);
            result_block.append(res);
            result_block.show(100);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }else{
            result_block.show(1000);
            result_block.html(`<div>В программе <span>"${title}"</span> нет ни одного курса: <div class="success">`);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }
    })
})

/*--------------------------------*/


/*-------View Program Keys List-------*/

jQuery(document).on('click', '.cd_key_program .cd__programs_item', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__program_details');
    const title = jQuery(this).find('.cd__programs_item_title').html();
    cd__content_request (program_id, 'cd__get_key_programs_details' ).then((res) => {
        if(res){
            result_block.html(`<div class="success">Для программы <span>"${title}"</span> имеются следующие ключи: </div>`);
            result_block.append(res);
            result_block.show(100);
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }else{
            result_block.show(1000);
            result_block.html(`<div class="warning">Для программы <span>"${title}"</span> у вас нет ни одного ключа. </div>`);
            result_block.append(`<div style="margin: 60px 0; display: flex;">
        <button data-program_id="${program_id}" data-action="cd__create_and_attach_key">Сгенерировать +1 код (техническая функция)</button>
        <div class="cd__create_and_attach_key_result"></div>
    </div>`)
            jQuery('html, body').animate({ scrollTop: result_block.offset().top }, 'slow');
        }
    })
})

/*--------------------------------*/


/*-------cd__create_and_attach_key-------*/

jQuery(document).on('click', '[data-action="cd__create_and_attach_key"]', function(){
    const program_id = jQuery(this).data('program_id');
    const result_block = jQuery('.cd__create_and_attach_key_result');

    cd__content_request (program_id, 'cd__create_and_attach_key' ).then((res) => {
        if(res){
            result_block.html(res);
        }else{
            result_block.html(`Ошибка`)
        }
    })
})

/*--------------------------------*/


/*-------Add course to director-----*/

jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="add_course_to_director"]', function(){
        const course_id = jQuery(this).parent().find('input').val();
        const result_block = jQuery('.cd__steps_warnings');
        cd__content_request (course_id, 'cd__add_course_to_director' ).then(res=>result_block.html(res));
    })
})

/*----------------------------------*/


/*-------Student register key-------*/
jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="add_program_to_student"]', function(){
        const resultBlock = jQuery('.cd_add_program_to_student_result');
        const key = jQuery(this).parent().find('input').val();
        cd__key_request(key, 'cd__connect_student_with_program' ).then((res)=>{

            switch (res){
                case 'key_error':
                    resultBlock.html('Ошибка! Неверный код доступа');
                    break;
                case 'exist_before':
                    resultBlock.html('Ошибка! Ключ уже был использован ранее');
                    break;
                case 'success':
                    resultBlock.html('Ключ успешно активирован!');
                    window.location.reload();
                    break;
                case 'error':
                    resultBlock.html('Ошибка');
                    break;
            }

        });
    });
});
/*----------------------------------*/

/*-------Render Student list details-------*/
jQuery(document).ready(function(){
    jQuery(document).on('click','[data-action="show_program_students"]', function(){
        const resultBlock = jQuery('.cd__program_details');
        $program_id = jQuery(this).data('program_id');
        resultBlock.show();
        resultBlock.html(loaderHtml);
        cd__content_request ($program_id, 'cd__get_students_control_details' ).then((res)=>{
            if(res === 'not_found'){
                resultBlock.html('Пользователей не найдено');
            }else{
                resultBlock.html(res);
            }

        });
    });
});
/*----------------------------------*/

