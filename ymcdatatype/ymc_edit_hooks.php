<?php
/**
 * File containing global hook functions.
 *
 * WARNING: Ask ymc-dabe before editing this file!!!
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Hooks for the content editing.
 *
 * The hooks are needed for the node placement functionality of the enhanced
 * selection datatype.
 *
 * This hooks need to be registered by a patch in
 * ezroot/kernel/content/edit.php:
 * 
 * <code>
 *  -29,8 +29,12
 *   //include_once( "lib/ezutils/classes/ezini.php" );
 *   $Module = $Params['Module'];
 *   require 'kernel/content/node_edit.php';
 *   initializeNodeEdit( $Module );
 *  +//by ymc-dabe //PATCH: We need to register our own module-hocks //start
 *  +ymcDatatypeEditHooks::initialize( $Module );
 *  +//by ymc-dabe //PATCH: We need to register our own module-hocks //end
 *   require 'kernel/content/relation_edit.php';
 *   initializeRelationEdit( $Module );
 *   require 'kernel/content/section_edit.php';
 *   initializeSectionEdit( $Module );
 * </code>
 *
 * See also: doc/features/3.5/node_assignment.txt
 * 
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @author     ymc-toko <thomas.koch@ymc.ch> 
 * @license    --ymc-unclear---
 */
class ymcDatatypeEditHooks
{
    /**
     * Register the hooks in the module.
     *
     * The addHook method can be found in lib/ezutils/classes/ezmodule.php
     * The hooks are called via eZModule::runHooks in
     * kernel/content/attribute_edit.php
     * 
     * @param mixed $module Is eZModule.
     *
     * @return void
     */
    public static function initialize( $module )
    {
        $hooks = new self;
        $module->addHook( 'post_fetch',
                          array( $hooks,
                                 'addNodeAssignments' )
                        );
        $module->addHook( 'post_store',
                          array( $hooks,
                                 'removeNodeAssignments' )
                        );
        $module->addHook( 'pre_template',
                           array( $hooks,
                                  'handleTemplate' )
                        );
    }

    /**
     * Add an object to an array of nodes.
     *
     * The array of nodes, is given by the post field ymcActiveNodeAssignmentsPool[]
     *
     * This hook is run at "post_fetch" time.
     * 
     * @param mixed  $module                  Is eZModule.
     * @param mixed  $class                   Is eZContentClass.
     * @param mixed  $object                  Is eZContentObject.
     * @param mixed  $version                 Is eZContentObjectVersion.
     * @param mixed  $contentObjectAttributes Is eZContentObjectAttribute.
     * @param string $editVersion             Number as String.
     * @param string $editLanguage            E.g. eng-GB.
     * @param mixed  $fromLanguage            Or false.
     * @param mixed  &$validation             Array.
     *
     * @return void
     */
    public function addNodeAssignments( $module,
                                        $class,
                                        $object,
                                        $version,
                                        $contentObjectAttributes,
                                        $editVersion,
                                        $editLanguage,
                                        $fromLanguage,
                                        &$validation )
    {
        $http = eZHTTPTool::instance();
        
        // UseNodeAssignments is only set in two templates, where it is 0:
        // design/admin/templates/content/edit.tpl
        // design/admin/override/templates/content/template_look_edit.tpl
        //
        // This means, that we exit here, if we come from these templates.
        if ( $http->hasPostVariable( 'UseNodeAssigments' ) )
        {
//@todo: Should we really return here? Wouldn't it be better to trigger the
//add and removal actions also from the admin interface?            
//            return;
        }
        
        $ObjectID = $object->attribute( 'id' );
        // Assign to nodes
        if (    $http->hasPostVariable('ymcActiveNodeAssignmentsPool') 
             && is_array($http->postVariable('ymcActiveNodeAssignmentsPool')) )
        {
            $selectedNodeIDArray = $http->postVariable('ymcActiveNodeAssignmentsPool');

            $assignedIDArray = array();
            $setMainNode = false;
            $hasMainNode = false;

            // * Get all nodes, to which the object is already assigned to and
            //   put them in $assignedIDArray[]
            // 
            // * Also removed assignments, but did not understant this.
            //
            // nodeAssignments returns array of eZNodeAssignment
            foreach ( $version->nodeAssignments() as $assignedNode )
            {
                $assignedNodeID = $assignedNode->attribute( 'parent_node' );
                if ( $assignedNode->attribute( 'is_main' ) )
                {
                    $hasMainNode = true;
                }

                if (    $assignedNode->attribute('op_code') === eZNodeAssignment::OP_CODE_REMOVE
                     && in_array($assignedNodeID, $selectedNodeIDArray) )
                {
                    if ( $assignedNode->attribute( 'is_main' ) )
                    {
                        $hasMainNode = false;
                    }
                    eZNodeAssignment::purgeByID( $assignedNode->attribute( 'id' ) );
                }
                else
                {
                    $assignedIDArray[] = $assignedNodeID;
                }
            }
            
            if ( !$hasMainNode )
            {
                $setMainNode = true;
            }
            
            foreach ( $selectedNodeIDArray as $nodeID )
            {
                if ( (int)$nodeID > 0 and !in_array( (int)$nodeID, $assignedIDArray ) )
                {
                    $nodeID = (int)$nodeID;
                    $isPermitted = true;
                    // Check access
                    $newNode = eZContentObjectTreeNode::fetch( $nodeID );
                    if ( is_object($newNode) )
                    {
                        $newNodeObject = $newNode->attribute( 'object' );
        
                        $canCreate = $newNodeObject->checkAccess( 'create',
                                                                  $class->attribute( 'id' ),
                                                                  $newNodeObject->attribute( 'contentclass_id' ) ) == 1;
                        if ( !$canCreate )
                        {
                            $isPermitted = false;
                        }
                        else
                        {
                            $canCreateClassList = $newNodeObject->attribute( 'can_create_class_list' );
                            $objectClassID = $object->attribute( 'contentclass_id' );
                            $canCreateClassIDList = array();
                            foreach ( array_keys( $canCreateClassList ) as $key )
                            {
                                $canCreateClassIDList[] = $canCreateClassList[$key]['id'];
                            }
                            if ( !in_array( $objectClassID, $canCreateClassIDList ) )
                            {
                                $isPermitted = false;
                            }
                        }
                        if ( !$isPermitted )
                        {
                            eZDebug::writeError( $newNode->attribute( 'path_identification_string' ),
                                                 "[ymcEdit] You are not allowed to place this object under:" );
                            $validation[ 'placement' ][] = array( 'text' => ezi18n( 'kernel/content',
                                                                  'You are not allowed to place this object under: %1',
                                                                  null,
                                                                  array( $newNode->attribute( 'url_alias' ) ) ) );
                            $validation[ 'processed' ] = true;
                            // Error message.
                        }
                        else
                        {
                            $isMain = 0;
                            $db = eZDB::instance();
                            $db->begin();
                            $version->assignToNode( $nodeID, $isMain );
                            $db->commit();
                        }
                    }
                    else // The given Node id to place the object to, does not exist
                    {
                        eZDebug::writeError( $nodeID, "[ymcEdit] Tried to place an object on a non existing node with id:" );
                        $validation[ 'placement' ][] = array( 'text' => ezi18n('kernel/content', 'You can not place this object on a non existing location') );
                        $validation[ 'processed' ] = true;
                        // Error message.
                    }
                }
            }
            if ( $setMainNode )
            {
                eZNodeAssignment::setNewMainAssignment( $object->attribute( 'id' ), $version->attribute( 'version' ) );
            }
        }
    }

    /**
     * Removes previously selected, now unselected assignments.
     * 
     * This hook is run at "post_store" time.
     *
     * @param mixed  $module                  Is eZModule.
     * @param mixed  $class                   Is eZContentClass.
     * @param mixed  $object                  Is eZContentObject.
     * @param mixed  $version                 Is eZContentObjectVersion.
     * @param mixed  $contentObjectAttributes Is eZContentObjectAttribute.
     * @param string $editVersion             Number as String.
     * @param string $editLanguage            E.g. eng-GB.
     * @param mixed  $fromLanguage            Or false.
     * @param mixed  &$validation             Array.
     * 
     * @return int eZModule::HOOK_STATUS_CANCEL_RUN
     */
    public function removeNodeAssignments( $module,
                                           $class,
                                           $object,
                                           $version,
                                           $contentObjectAttributes,
                                           $editVersion,
                                           $editLanguage,
                                           $fromLanguage,
                                           &$validation )
    {
        $http = eZHTTPTool::instance();
        
        if ( !$http->hasPostVariable('ymcNodeAssignmentsPool') )
        {
            return eZModule::HOOK_STATUS_OK;
        }

        $ymcActiveNodeAssignmentsPool = $http->postVariable('ymcActiveNodeAssignmentsPool');
        $ymcNodeAssignmentsPool       = $http->postVariable('ymcNodeAssignmentsPool');
        
        if ( !is_array($ymcActiveNodeAssignmentsPool) )
        {
            $ymcActiveNodeAssignmentsPool = array();
        }
        
        if ( !is_array($ymcNodeAssignmentsPool) )
        {
            $ymcNodeAssignmentsPool = array();
        }
        
        $selected = array();
        foreach ( $ymcNodeAssignmentsPool as $ymcAllNodeAssignment )
        {
            if (     (int)$ymcAllNodeAssignment > 0 
                 and !in_array($ymcAllNodeAssignment, $ymcActiveNodeAssignmentsPool) )
            {
                $selected[] = (int)$ymcAllNodeAssignment;
            }
        }
        
        $objectID       = $object->attribute( 'id' );
        $versionInt     = $version->attribute( 'version' );
        $hasChildren    = false;
        $assignmentsIDs = array();
        $assignments    = array();
        
        // Determine if at least one node of ones we remove assignments for has children.
        foreach ( $selected as $parentNodeID )
        {
            $assignment = eZNodeAssignment::fetch( $objectID, $versionInt, $parentNodeID );
            if( !$assignment )
            {
                eZDebug::writeWarning( "[ymcEdit] No assignment found for object $objectID version $versionInt,
                                       parent node $parentNodeID" );
                continue;
            }

            $assignmentID     =  $assignment->attribute( 'id' );
            $assignmentsIDs[] =  $assignmentID;
            $assignments[]    =& $assignment;
            $node             =& $assignment->attribute( 'node' );
            if( !$node )
            {
                continue;
            }
            
            if( $node->childrenCount( false ) > 0 )
            {
                $hasChildren = true;
            }
            
            unset( $assignment );
        }
        
        if ( $hasChildren )
        {
            // We need user confirmation if at least one node we want to
            // remove assignment for contains children.  Aactual removal is
            // done in content/removeassignment in this case.
            $http->setSessionVariable( 'AssignmentRemoveData',
                                       array( 'remove_list'   => $assignmentsIDs,
                                              'object_id'     => $objectID,
                                              'edit_version'  => $versionInt,
                                              'edit_language' => $editLanguage,
                                              'from_language' => $fromLanguage ) );
            $module->redirectToView( 'removeassignment' );
            return eZModule::HOOK_STATUS_CANCEL_RUN;
        }
        else
        {
            // Just remove all the selected locations.
            $mainNodeChanged = false;
            $db = eZDB::instance();
            $db->begin();
            foreach ( $assignments as $assignment )
            {
                $assignmentID = $assignment->attribute( 'id' );
                if ( $assignment->attribute( 'is_main' ) )
                {
                    $mainNodeChanged = true;
                }
                eZNodeAssignment::removeByID( $assignmentID );
            }
            if ( $mainNodeChanged )
            {
                eZNodeAssignment::setNewMainAssignment( $objectID, $versionInt );
            }
            $db->commit();
            unset( $mainNodeChanged );
        }
        unset( $assignmentsIDs, $assignments );
    }

    /**
     * Sets Template var to indicate, to which nodes an object already belongs. 
     *
     * The template var ymc_edit_current_parent_node_ids is used in the
     * following templates under /ugc_volano/experimental/ :
     * 
     * ugc/design/musicnight/override/templates/edit/musicnight_video_meta.tpl
     * ugc/design/volltreffer/override/templates/edit/volltreffer_video_meta.tpl
     * ymccommon_openvolano/content/ymc_edit.php
     * <code>
     *  <input type="checkbox"
     *         name="ymcActiveNodeAssignmentsPool[]" 
     *         value="{$additional_placement_object.main_node_id}"
     *         {if $ymc_edit_current_parent_node_ids|contains($additional_placement_object.main_node_id)}
     *              checked="checked"
     *         {/if} 
     *   />
     * </code>
     *
     * This hook is run at "pre_template" time.
     * 
     * @param mixed  $module                  Is eZModule.
     * @param mixed  $class                   Is eZContentClass.
     * @param mixed  $object                  Is eZContentObject.
     * @param mixed  $version                 Is eZContentObjectVersion.
     * @param mixed  $contentObjectAttributes Is eZContentObjectAttribute.
     * @param string $editVersion             Number as String.
     * @param string $editLanguage            E.g. eng-GB.
     * @param mixed  &$tpl                    Is eZTemplate.
     *
     * @return void
     */
    public function handleTemplate( $module,
                                    $class,
                                    $object,
                                    $version,
                                    $contentObjectAttributes,
                                    $editVersion,
                                    $editLanguage,
                                    &$tpl )
    {
        $assignedNodeIDArray = array();
        foreach ( $version->attribute('node_assignments') as $nodeAssignment )
        {
            if ( $nodeAssignment->attribute( 'op_code' ) === eZNodeAssignment::OP_CODE_REMOVE )
            {
                continue;
            }
            $assignedNodeIDArray[] = $nodeAssignment->attribute('parent_node');
        }
        
        $tpl->setVariable( 'ymc_edit_current_parent_node_ids', $assignedNodeIDArray );
    }
}
?>
