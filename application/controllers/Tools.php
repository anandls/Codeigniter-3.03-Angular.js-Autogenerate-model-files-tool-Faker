<?php

class Tools extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // can only be called from the command line
        if (!$this->input->is_cli_request()) {
            exit('Direct access is not allowed. This is a command line tool, use the terminal');
        }
        $this->load->dbforge();
        // initiate faker
        $this->faker = Faker\Factory::create();
    }

    public function message($to = 'World') {
        echo "Hello {$to}!" . PHP_EOL;
    }

    public function help() {
        $result = "The following are the available command line interface commands\n\n";
        $result .= "php index.php Tools generateModels                  Auto generate models based on database\n";
        $result .= "php index.php Tools migration \"file_name\"         Create new migration file\n";
        $result .= "php index.php Tools migrate [\"version_number\"]    Run all migrations. The version number is optional.\n";
        $result .= "php index.php Tools seeder \"file_name\"            Creates a new seed file.\n";
        $result .= "php index.php Tools seed \"file_name\"              Run the specified seed file.\n";

        echo $result . PHP_EOL;
    }
    
    public function generateModels(){
    /*Database Setting Start*/
	$host = $this->db->hostname;
	$port = $this->db->port;
	$user = $this->db->username;
	$pass = $this->db->password;
    $db_name = $this->db->database;
    /*Database Setting End*/

    /*Extracting Folder Start*/
    $extract_folder = "./application/models";
    if (!file_exists($extract_folder)) {
        mkdir($extract_folder, 0755, true);
    }
    /*Extracting Folder End*/

    /*Database Connection Start*/
    $conn = mysqli_connect($host, $user, $pass, $db_name);

    $sql = "select table_name from information_schema.tables where table_schema='$db_name'";
    $result = mysqli_query($conn, $sql);
    /*Database Connection End*/


    /*Generating Model Code Start*/
    while($row = $result->fetch_assoc())
    {
        $tb_name = $row['table_name'];
        $sql = "select column_name, column_key, extra from information_schema.columns where table_schema='$db_name' and table_name='$tb_name'";
        $result_column = mysqli_query($conn, $sql);

        $total = mysqli_query($conn, "SELECT * FROM $tb_name");
        $total_num = $total->num_rows;

        $ftable = fopen($extract_folder.'/'."Model_".strtolower($tb_name) . ".php", "w");
        $str = "<?php\n";
        $str .= "class " . "Model_" . ucfirst($tb_name) . " extends CI_Model{\n\n\t";
        $str .= "function __construct(){\n\t\t";
        $str .= "parent::__construct();\n\t}\n\n\t";
        fwrite($ftable, $str);

        $str_create = "function create(\$item){\n\t\t";
        $str_read = "function get(\$id){\n\t\t";
        $str_readAll = "function get_all(){\n\t\t";
        $str_update = "function update(\$id, \$item){\n\t\t";
        $str_delete = "function delete(\$id){\n\t\t";

        $str_create .= "\$data = array(\n\t\t\t";
        $str_update .= "\$data = array(\n\t\t\t";
        $str_update_col = "";
        
        $index=1;
        
        while($row_column = $result_column->fetch_assoc()){
            if($row_column["extra"] != "auto_increment")
            {
                if($index!=$total_num){
                    $str_create .= "'" . $row_column['column_name'] . "' => \$item['" . $row_column['column_name'] . "'],\n\t\t\t";
                }else{
                    $str_create .= "'" . $row_column['column_name'] . "' => \$item['" . $row_column['column_name'] . "']\n\t\t\t";
                }
            }
            if($row_column["column_key"] != "PRI"){
                if($index!=$total_num){
                    $str_update .= "'" . $row_column['column_name'] . "' => \$item['" . $row_column['column_name'] . "'],\n\t\t\t";
                }else{
                    $str_update .= "'" . $row_column['column_name'] . "' => \$item['" . $row_column['column_name'] . "']\n\t\t\t";
                }
            }else{
                $str_read .= "\$this->db->select('*');\n\t\t";
                $str_read .= "\$this->db->from('" . strtolower($tb_name) . "');\n\t\t";
                $str_read .= "\$this->db->where('" . $row_column['column_name'] . "', \$id);\n\t\t";
                $str_delete .= "\$this->db->where('" . $row_column['column_name'] . "', \$id);\n\t\t";
                $str_update_col = $row_column['column_name'];
            }
            $index++;
        }

        $str_create .= " ) \n\n\t\t";
        $str_create .= "\$this->db->insert('" . strtolower($tb_name) . "', \$data);\n\t";
        $str_create .= "}\n\n\t";
        $str_update .= " ) \n\n\t\t";
        $str_update .= "\$this->db->where('" . $str_update_col . "', \$id);\n\t\t";
        $str_update .= "\$this->db->update('" . strtolower($tb_name) . "', \$data);\n\t";
        $str_update .= "}\n\n\t";
        $str_delete .= "\$this->db->delete('" . strtolower($tb_name) . "');\n\t";
        $str_delete .= "}\n";
        $str_read .= "\$query = \$this->db->get();\n\n\t\t";
        $str_read .= "if(\$query->num_rows()<1){\n\t\t\t";
        $str_read .= "return null;\n\t\t";
        $str_read .= "}";
        $str_read .= "else{\n\t\t\t";
        $str_read .= "return \$query->row();\n\t\t";
        $str_read .= "}\n\t";
        $str_read .= "}\n\n\t";
        $str_readAll .= "\$this->db->select('*');\n\t\t";
        $str_readAll .= "\$this->db->from('" . strtolower($tb_name) . "');\n\t\t";
        $str_readAll .= "\$query = \$this->db->get();\n\n\t\t";
        $str_readAll .= "if(\$query->num_rows()<1){\n\t\t\t";
        $str_readAll .= "return null;\n\t\t";
        $str_readAll .= "}";
        $str_readAll .= "else{\n\t\t\t";
        $str_readAll .= "return \$query->result_array();\n\t\t";
        $str_readAll .= "}\n\t";
        $str_readAll .= "}\n\n\t";

        fwrite($ftable, $str_create);
        fwrite($ftable, $str_read);
        fwrite($ftable, $str_readAll);
        fwrite($ftable, $str_update);
        fwrite($ftable, $str_delete);
        fwrite($ftable, "}");
        fclose($ftable);
        /*Generating Model Code End*/
    }
    }

    public function migration($name) {
        $this->make_migration_file($name);
    }

    public function migrate($version = null) {
        $this->load->library('migration');

        if ($version != null) {
            if ($this->migration->version($version) === FALSE) {
                show_error($this->migration->error_string());
            }else{
                echo "Migrations run successfully" . PHP_EOL;
            }return;
        }

        if($this->migration->latest() === FALSE){
            show_error($this->migration->error_string());
        }else{
            echo "Migrations run successfully" . PHP_EOL;
        }
    }

    public function seeder($name){
        $this->make_seed_file($name);
    }

    public function seed($name){
        $seeder = new Seeder();
        $seeder->call($name);
    }

    protected function make_migration_file($name){
        $date = new DateTime();
        $timestamp = $date->format('YmdHis');
        $table_name = strtolower($name);
        $path = APPPATH . "database/migrations/$timestamp" . "_" . "$name.php";
        $my_migration = fopen($path, "w") or die("Unable to create migration file!");
        $migration_template = "<?php
class Migration_$name extends CI_Migration {
    public function up() {
        \$this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            )
        ));
        \$this->dbforge->add_key('id', TRUE);
        \$this->dbforge->create_table('$table_name');
    }

    public function down() {
        \$this->dbforge->drop_table('$table_name');
    }

}";

        fwrite($my_migration, $migration_template);
        fclose($my_migration);
        echo "$path migration has successfully been created." . PHP_EOL;
    }

    protected function make_seed_file($name) {
        $path = APPPATH . "database/seeds/$name.php";
        $my_seed = fopen($path, "w") or die("Unable to create seed file!");
        $seed_template = "<?php
class $name extends Seeder {

    private \$table = 'users';
    public function run() {
        \$this->db->truncate(\$this->table);
        //seed records manually
        \$data = [
            'user_name' => 'admin',
            'password' => '9871'
        ];
        \$this->db->insert(\$this->table, \$data);

        //seed many records using faker
        \$limit = 33;
        echo \"seeding \$limit user accounts\";

        for (\$i = 0; \$i < \$limit; \$i++) {
            echo \".\";

            \$data = array(
                'user_name' => \$this->faker->unique()->userName,
                'password' => '1234',
            );

            \$this->db->insert(\$this->table, \$data);
        }

        echo PHP_EOL;
    }
}
";

        fwrite($my_seed, $seed_template);
        fclose($my_seed);
        echo "$path seeder has successfully been created." . PHP_EOL;
    }

}
