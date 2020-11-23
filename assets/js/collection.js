
/**
 * [ramphor_collection_find_tag_data description]
 *
 * @param   {HTMLElement}  element         [element description]
 * @param   {string}  className  [className description]
 *
 * @return  {HTMLElement}                  [return description]
 */
function ramphor_collection_find_tag_data(element, className, max_loop = 10) {
    if (element.className.indexOf(className) >= 0) {
        return element;
    }
    if (max_loop > 0) {
        return ramphor_collection_find_tag_data(element.parentNode, className, max_loop - 1);
    }
}

/**
 * [ramphor_collection_extract_data description]
 *
 * @param   {HTMLElement}  element  [element description]
 *
 * @return  {[type]}           [return description]
 */
function ramphor_collection_extract_data(element) {
    if (typeof element.attributes['data-collection'] === undefined) {
        return {};
    }
    return {
        collection: element.dataset.collection,
        post_id: element.dataset.postId,
        nonce: element.dataset.nonce,
        action: element.dataset.collectionAction
    };
}

/**
 * [ramphor_collection_call_action description]
 *
 * @param   {MouseEvent}  event  [event description]
 */
function ramphor_collection_call_action(event) {
    event.preventDefault();
    var data_tag = ramphor_collection_find_tag_data(event.target, 'collection-action');
    var data = ramphor_collection_extract_data(data_tag);
    var ramphor_collections = window.ramphor_collections || {};
    var xhr = new XMLHttpRequest();
    /**
     * [addEventListener description]
     *
     * @param   {[type]}  load      [load description]
     * @param   {[type]}  function  [function description]
     * @param   {XMLHttpRequestResponseType}  e         [e description]
     *
     * @return  {[type]}            [return description]
     */
    xhr.addEventListener('load', function(e){
        if (!e.target) {
            e.target = {};
        }
        e.target.responseJSON = JSON.parse(e.target.response);
        var response = e.target.responseJSON;
        var not_logged_in_callback = window[ramphor_collections.user_not_loggedin_callback];

        if (!response.success) { // Error case
            if (response.data.error === 'not_logged_in' && typeof not_logged_in_callback === 'function') {
                not_logged_in_callback(data);
            } else if (response.data.message) {
                alert(response.data.message);
            }
            return;
        }
        data_tag.innerHTML = response.data.new_html;
        data_tag.dataset.collectionAction = data.action === 'add' ? 'remove' : 'add';
    });
    xhr.open(
        'POST',
        data.action === 'add'
            ? ramphor_collections.add_post_to_collection
            : ramphor_collections.remove_post_to_collection
    );
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
}

window.addEventListener('DOMContentLoaded', function() {
    var collectionPostActions = document.querySelectorAll('[data-collection-action]');
    if (collectionPostActions.length > 0) {
        for(i = 0;i < collectionPostActions.length; i+=1) {
            var collectionPostAction = collectionPostActions[i];
            collectionPostAction.addEventListener('click', ramphor_collection_call_action);
        }
    }
});