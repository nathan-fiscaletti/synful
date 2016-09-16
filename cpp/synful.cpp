
/**
 * This C++ file is used to generate the main Synful binary for managing your API
 */

#include <iostream>
#include <sys/stat.h>
#include <unistd.h>

/**
 * Check if a file exists
 * @param  name The name of the file
 * @return boolean
 */
static bool exists (const std::string& name) 
{
  struct stat buffer;   
  return (stat (name.c_str(), &buffer) == 0); 
}

/**
 * Initialize a new instance of the synful binary
 * 
 * @param  argc 
 * @param  argv
 * @return int
 */
int main(int argc, char *argv[])
{
  if (! exists("./src/Synful/Synful.php")) {
    std::cout << "Must be run from Synful root directory.\r\n";
    exit(1);
  }

  std::string commandLineStr= "cd public/;php index.php ";
  for (int i = 1;i<argc;i++) {
    commandLineStr.append(std::string(argv[i]).append(" "));  
  }
  commandLineStr.append(";cd ../;");
  system(commandLineStr.c_str());
}
