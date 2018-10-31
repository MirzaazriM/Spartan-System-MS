<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/16/18
 * Time: 7:27 PM
 */

namespace Model\Mapper;

use PDO;
use Component\DataMapper;

class MigrationMapper extends DataMapper
{

    public function migration(){

        $this->updateSameTables();
        $this->setExerciseTables();
        $this->setPackagePlans();
        $this->setPlans();
    }


    public function setPlans(){

        try {

            // get nutrition plan ids
            $sql = "SELECT GROUP_CONCAT(plan_parent) AS nut_plans FROM spartan_data.plan_nutrition";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $nutritionIds = explode(',', $statement->fetch()[0]);

            // get workout plan ids
            $sql = "SELECT GROUP_CONCAT(plan_parent) AS wor_plans FROM spartan_data.plan_workout";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $workoutIds = explode(',', $statement->fetch()[0]);



            // get all plans and divide them to workout and nutrition plans
            $sqlPlan = "SELECT * FROM spartan_data.plan";
            $statementPlan = $this->connection->prepare($sqlPlan);
            $statementPlan->execute();



            /* PLANS */

            // loop through data and set nutrition and workout plans
            while($row = $statementPlan->fetch(PDO::FETCH_ASSOC)){

                $planId = $row['id'];

                if(in_array($planId, $nutritionIds)){
                    $sql = "INSERT INTO recepie_plans 
                              (id, thumbnail, raw_name, type, state, version)
                              VALUES (?,?,?,?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['id'],
                        $row['thumbnail'],
                        $row['raw_name'],
                        $row['type'],
                        $row['state'],
                        $row['version']
                    ]);

                }else {
                    $sql = "INSERT INTO workout_plans 
                              (id, thumbnail, raw_name, type, state, version)
                              VALUES (?,?,?,?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['id'],
                        $row['thumbnail'],
                        $row['raw_name'],
                        $row['type'],
                        $row['state'],
                        $row['version']
                    ]);
                }
            }



            // insert plan names
            $sqlPlanName = "SELECT * FROM spartan_data.plan_name";
            $statementPlanName = $this->connection->prepare($sqlPlanName);
            $statementPlanName->execute();

            while($row = $statementPlanName->fetch(PDO::FETCH_ASSOC)){

                $parent = $row['plan_parent'];


                if(in_array($parent, $nutritionIds)){
                    $sql = "INSERT INTO recepie_plans_names 
                              (name, language, recepie_plans_parent)
                              VALUES (?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['name'],
                        $row['language'],
                        $parent
                    ]);
                }else {
                    $sql = "INSERT INTO workout_plans_names 
                              (name, language, workout_plans_parent)
                              VALUES (?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['name'],
                        $row['language'],
                        $parent
                    ]);
                }


            }


            // insert plan descriptions
            $sqlPlanDesc = "SELECT * FROM spartan_data.plan_description";
            $statementPlanDesc = $this->connection->prepare($sqlPlanDesc);
            $statementPlanDesc->execute();

            while($row = $statementPlanDesc->fetch(PDO::FETCH_ASSOC)){

                $parent = $row['plan_parent'];

                if(in_array($parent, $nutritionIds)){
                    $sql = "INSERT INTO recepie_plans_descriptions
                              (description, language, recepie_plans_parent)
                              VALUES (?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['name'],
                        $row['language'],
                        $parent
                    ]);
                }else {
                    $sql = "INSERT INTO workout_plans_descriptions 
                              (description, language, workout_plans_parent)
                              VALUES (?,?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $row['name'],
                        $row['language'],
                        $parent
                    ]);
                }
            }


            // insert plan tags
            $sqlPlanTag = "SELECT * FROM spartan_data.plan_tags";
            $statementPlanTag = $this->connection->prepare($sqlPlanTag);
            $statementPlanTag->execute();
            // insert them
            while($row = $statementPlanTag->fetch(PDO::FETCH_ASSOC)){

                $parent = $row['plan_parent'];

                if(in_array($parent, $nutritionIds)){
                    $sql = "INSERT INTO recepie_plans_tags
                              (recepie_plans_parent,	tag_id)
                              VALUES (?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $parent,
                        $row['tag_id']
                    ]);
                }else {
                    $sql = "INSERT INTO workout_plans_tags
                              (workout_plans_parent, tag_id)
                              VALUES (?,?)";
                    $statement = $this->connection->prepare($sql);
                    $statement->execute([
                        $parent,
                        $row['tag_id']
                    ]);
                }

            }

            // insert workout plan workouts
            $sqlWorkouts = "SELECT * FROM spartan_data.plan_workout";
            $statementWorkouts = $this->connection->prepare($sqlWorkouts);
            $statementWorkouts->execute();
            // insert them
            while($row = $statementWorkouts->fetch(PDO::FETCH_ASSOC)){

                $sql = "INSERT INTO workout_plans_workouts
                              (workout_plans_parent, workout_id)
                              VALUES (?,?)";
                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $row['plan_parent'],
                    $row['workout']
                ]);
            }


        }catch(\PDOException $e){
            die($e->getMessage());
        }

    }

    /**
     * Set package plans
     */
    public function setPackagePlans(){

        try {

            // get nutrition plan ids
            $sql = "SELECT GROUP_CONCAT(plan_parent) AS nut_plans FROM spartan_data.plan_nutrition";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $nutritionIds = explode(',', $statement->fetch()[0]);

            // get package plans ids
            $sql = "SELECT package_parent, plan_child, state FROM spartan_data.package_plans";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            // insert plans
            $sqlPlan = "INSERT INTO package_plans 
                          (package_parent, workout_plan_child, recepie_plan_child, state)
                          VALUES (?,?,?,?)";
            $statementPlan = $this->connection->prepare($sqlPlan);

            // loop through data
            while($row = $statement->fetch(PDO::FETCH_ASSOC)){

                $planChild = $row['plan_child'];

                if(in_array($planChild, $nutritionIds)){
                    $statementPlan->execute([
                        $row['package_parent'],
                        null,
                        $planChild,
                        $row['state']
                    ]);
                }else {
                    $statementPlan->execute([
                        $row['package_parent'],
                        $planChild,
                        null,
                        $row['state']
                    ]);
                }
            }

        }catch(\PDOException $e){
            die($e->getMessage());
        }
    }

    /**
     * Update same tables
     */
    public function updateSameTables(){

        try {

            // insert apps data
            $sql = "INSERT INTO Spartan.apps (id, name, identifier, date)
                      SELECT id, name, identifier, date FROM spartan_data.apps
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert apps audit
            $sql = "INSERT INTO Spartan.app_audit (old_name, new_name, app_parent)
                      SELECT old_name, new_name, app_parent FROM spartan_data.app_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert app_packages data
            $sql = "INSERT INTO Spartan.app_packages (id, app_parent, package_child, sku)
                      SELECT id, app_parent, package_child, sku FROM spartan_data.app_packages
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert app_package names
            $sql = "INSERT INTO Spartan.app_package_name (id, app_parent, package_name, type)
                      SELECT id, app_parent, package_name, type FROM spartan_data.app_package_name
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert app_response_caches
            $sql = "INSERT INTO Spartan.app_response_caches (app_identifier, cached, language, path, kind, version)
                      SELECT app_identifier, cached, language, path, kind, version FROM spartan_data.app_response_caches
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();



            // OAUTH TABLES
            $sql = "INSERT INTO Spartan.oauth_access_tokens (access_token, client_id, user_id, expires, scope)
                      SELECT access_token, client_id, user_id, expires, scope FROM spartan_data.oauth_access_tokens
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_api_limit (user_name, date, permission)
                      SELECT user_name, date, permission FROM spartan_data.oauth_api_limit
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_authorization_codes (authorization_code, client_id, user_id, redirect_uri, expires, scope, id_token)
                      SELECT authorization_code, client_id, user_id, redirect_uri, expires, scope, id_token FROM spartan_data.oauth_authorization_codes
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_clients (client_id, client_secret, redirect_uri, grant_types, scope, user_id, name, description, type, image)
                      SELECT client_id, client_secret, redirect_uri, grant_types, scope, user_id, name, description, type, image FROM spartan_data.oauth_clients
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_jwt (client_id, subject, public_key)
                      SELECT client_id, subject, public_key FROM spartan_data.oauth_jwt
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_refresh_tokens (refresh_token, client_id, user_id, expires, scope)
                      SELECT refresh_token, client_id, user_id, expires, scope FROM spartan_data.oauth_refresh_tokens
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_scopes (scope, is_default)
                      SELECT scope, is_default FROM spartan_data.oauth_scopes
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            $sql = "INSERT INTO Spartan.oauth_users (username, password, first_name, last_name, email, scope, image, api_limit)
                      SELECT username, password, first_name, last_name, email, scope, image, api_limit FROM spartan_data.oauth_users
            ";
            $statement = $this->connection->prepare($sql);
            $statement->execute();



            // insert exercise names
            $sql = "INSERT INTO Spartan.exercies_name (id, name, language, exercise_parent, state)
                      SELECT id, name, language, exercise_parent, state FROM spartan_data.exercies_name
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert exercise tags
            $sql = "INSERT INTO Spartan.exercise_tag (id, exercise_parent, tag_id)
                      SELECT id, exercise_parent, tag_id FROM spartan_data.exercise_tag
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert languages data
            $sql = "INSERT INTO Spartan.language (id, name, code)
                      SELECT id, name, code FROM spartan_data.language
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert packages data
            $sql = "INSERT INTO Spartan.package (id, thumbnail, raw_name, state, version)
                      SELECT id, thumbnail, raw_name, state, version FROM spartan_data.package
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert packages audit
            $sql = "INSERT INTO Spartan.package_audit (old_thumbnail, new_thumbnail, old_raw_name, new_raw_name, package_parent)
                      SELECT old_thumbnail, new_thumbnail, old_raw_name, new_raw_name, package_parent FROM spartan_data.package_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert package descriptions
            $sql = "INSERT INTO Spartan.package_description (description, language, package_parent, state)
                      SELECT name, language, package_parent, state FROM spartan_data.package_description
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert package descriptions audit
            $sql = "INSERT INTO Spartan.package_description_audit (description, package_desc_parent)
                      SELECT name, package_desc_parent FROM spartan_data.package_description_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert package name
            $sql = "INSERT INTO Spartan.package_name (name, language, package_parent, state)
                      SELECT name, language, package_parent, state FROM spartan_data.package_name
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert package name audit
            $sql = "INSERT INTO Spartan.package_name_audit (name, package_name_parent)
                      SELECT name, package_name_parent FROM spartan_data.package_name_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert package tags
            $sql = "INSERT INTO Spartan.package_tags (package_parent, tag)
                      SELECT package_parent, tag FROM spartan_data.package_tags
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert tags
            $sql = "INSERT INTO Spartan.tag (id, behaviour, state, version)
                      SELECT id, behaviour, state, version FROM spartan_data.tag
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert tag audit
            $sql = "INSERT INTO Spartan.tag_audit (old_behaviour, new_behaviour, tag_parent)
                      SELECT old_behaviour, new_behaviour, tag_parent FROM spartan_data.tag_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert tag names
            $sql = "INSERT INTO Spartan.tag_name (id, name, language, tag_parent)
                      SELECT id, name, language, tag_parent FROM spartan_data.tag_name
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert tag names audit
            $sql = "INSERT INTO Spartan.tag_name_audit (name, tag_name_parent)
                      SELECT name, tag_name_parent FROM spartan_data.tag_name_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert terms
            $sql = "INSERT INTO Spartan.terms (id, title, body)
                      SELECT id, title, body FROM spartan_data.terms
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();

            // insert versions
            $sql = "INSERT INTO Spartan.version (id)
                      SELECT id FROM spartan_data.version
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workouts
            $sql = "INSERT INTO Spartan.workout (id, duration, state, version)
                      SELECT id, duration, state, version FROM spartan_data.workout
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout audit
            $sql = "INSERT INTO Spartan.workout_audit (old_duration, new_duration, workout_parent)
                      SELECT old_duration, new_duration, workout_parent FROM spartan_data.workout_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout descriptions
            $sql = "INSERT INTO Spartan.workout_description (description, language, workout_parent, state)
                      SELECT name, language, workout_parent, state FROM spartan_data.workout_description
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout descriptions audit
            $sql = "INSERT INTO Spartan.workout_description_audit (description, workout_desc_parent)
                      SELECT name, workout_desc_parent FROM spartan_data.workout_description_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout names
            $sql = "INSERT INTO Spartan.workout_name (name, language, workout_parent, state)
                      SELECT name, language, workout_parent, state FROM spartan_data.workout_name
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout names audit
            $sql = "INSERT INTO Spartan.workout_name_audit (name, workout_name_parent)
                      SELECT name, workout_name_parent FROM spartan_data.workout_name_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout rounds
            $sql = "INSERT INTO Spartan.workout_rounds (id, round, workout_id, exercises_id, duration, rest_duration, type, behaviour, state)
                      SELECT id, round, workout_id, exercises_id, duration, rest_duration, type, behaviour, state FROM spartan_data.workout_rounds
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout rounds audit
            $sql = "INSERT INTO Spartan.workout_rounds_audit (round, workout_id, exercises_id, duration, rest_duration, type, behaviour)
                      SELECT round, workout_id, exercises_id, duration, rest_duration, type, behaviour FROM spartan_data.workout_rounds_audit
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();


            // insert workout tags
            $sql = "INSERT INTO Spartan.workout_tags (workout_parent, tag_id, type)
                      SELECT workout_parent, tag_id, type FROM spartan_data.workout_tags
            ";

            $statement = $this->connection->prepare($sql);
            $statement->execute();

        }catch(\PDOException $e){
            die($e->getMessage());
        }
    }


    public function setExerciseTables(){
        // insert exercise data
        $sql = "INSERT INTO Spartan.exercises (id, hardness, muscles_invovled, thumbnail, raw_name, state, version)
                      SELECT id, hardness, muscles_invovled, thumbnail, raw_name, state, version FROM spartan_data.exercises
            ";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        // insert exercise media
        $sql = "SELECT id, gif, video FROM spartan_data.exercises";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        // loop through formats and insert them into corresponding table
        $sqlExer = "INSERT INTO exercise_media 
                      (type, source, exercise_parent) 
                      VALUES (?,?,?)";
        $statementExer = $this->connection->prepare($sqlExer);
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){

            if(!empty($row['gif'])){
                $statementExer->execute([
                    'gif',
                    $row['gif'],
                    $row['id']
                ]);
            }

            if(!empty($row['video'])){
                $statementExer->execute([
                    'mp4',
                    $row['video'],
                    $row['id']
                ]);
            }


        }
    }
}