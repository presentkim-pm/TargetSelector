# <img src="https://rawgit.com/PresentKim/SVG-files/master/plugin-icons/targetselector.svg" height="50" width="50"> TargetSelector  
__A plugin for [PMMP](https://pmmp.io) :: Implement target selector in PMMP!__ 
  
[![license](https://img.shields.io/github/license/PresentKim/TargetSelector-PMMP.svg?label=License)](./LICENSE)
[![release](https://img.shields.io/github/release/PresentKim/TargetSelector-PMMP.svg?label=Release)](../../releases/latest)
[![download](https://img.shields.io/github/downloads/PresentKim/TargetSelector-PMMP/total.svg?label=Download)](../../releases/latest)

## What is this?   
Target Selector is plugin that implement target selector in PMMP.  
More description of target selector Link : [Commands#Target_selectors](https://minecraft.gamepedia.com/Commands#Target_selectors)
  
You can see player inventory and modify too.  
Inventory monitor will be sync with player inventory.  
  
  
## Features  
- [x] Implement target selector  
  - [x] @p (nearest player)
  - [x] @r (random player)
  - [x] @a (all players)
  - [ ] @e (all entities)
  - [ ] @s (the entity executing the command)
  - [ ] Target selector arguments
- [x] Support configurable things  
- [x] Check that the plugin is not latest version  
  - [x] If not latest version, show latest release download url  
  
  
## Configurable things  
- [x] Configure the permission of target selector   
  - [x] in `config.yml` file  
- [x] Configure the whether the update is check (default "false")
  - [x] in `config.yml` file  
  
The configuration files is created when the plugin is enabled.  
The configuration files is loaded  when the plugin is enabled.  
