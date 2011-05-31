#!/usr/bin/python

import os, sys

currentPath = os.getcwd()
extensions = { '.mp4':1, '.avi':1, '.flv':1 }

def makethumbs():
   for dirname, dirnames, filenames in os.walk(os.getcwd()):
     for filename in filenames:
       nameExt = os.path.splitext(filename)
       if ( extensions.has_key(nameExt[1]) ):
         filepath = os.path.join(dirname, filename)
         print filepath
         command = "ffmpeg -i '" + filepath + "' -ss 1 -f image2 -vframes 1 -s 144x112 '" + dirname + "/thumb.jpg'"
         print command
         os.system(command)

def listpics():
   for dirname, dirnames, filenames in os.walk(os.getcwd()):
     for filename in filenames:
       nameExt = os.path.splitext(filename)
       if (nameExt[1] == '.jpg'):
         filepath = os.path.join(dirname, filename)
         print filepath

def listvids():
   for dirname, dirnames, filenames in os.walk(os.getcwd()):
     for filename in filenames:
       nameExt = os.path.splitext(filename)
       if ( extensions.has_key(nameExt[1])):
         filepath = os.path.join(dirname, filename)
         print filepath

def default_action():
   print 'specify an argument'
   exit(1)

#command line arguments
if ( (len(sys.argv) < 2) or (len(sys.argv) > 2) ):
   print 'usage: ./makethumbs.py [listpics/listvids/make]'
   exit(1)

paths = {
   'listpics': listpics,
   'listvids': listvids,
   'make': makethumbs,
}

paths.get(sys.argv[1], default_action)()
